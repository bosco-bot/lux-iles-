<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Villa;
use App\Models\VillaIcalConfig;
use App\Models\PlatformSync;
use App\Services\IcalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SynchronizationController extends Controller
{
    protected IcalService $icalService;

    public function __construct(IcalService $icalService)
    {
        $this->icalService = $icalService;
    }

    /**
     * Affiche la page de synchronisation
     */
    public function index()
    {
        // Statistiques par plateforme
        $platforms = ['airbnb', 'booking', 'vrbo'];
        $platformStats = [];
        
        foreach ($platforms as $platform) {
            $configs = VillaIcalConfig::where('platform', $platform)
                ->where('is_active', true)
                ->get();
            
            $totalSyncs = $configs->count();
            $successfulSyncs = $configs->where('last_sync_status', 'success')->count();
            $errorSyncs = $configs->where('last_sync_status', 'error')->count();
            
            $successRate = $totalSyncs > 0 ? round(($successfulSyncs / $totalSyncs) * 100, 1) : 0;
            
            // Dernière synchronisation
            $lastSync = $configs->whereNotNull('last_sync_at')
                ->sortByDesc('last_sync_at')
                ->first();
            
            // Déterminer le statut
            $status = 'connected';
            $statusText = 'Connecté';
            $statusClass = 'text-success';
            $statusIcon = 'fa-check-circle';
            
            if ($totalSyncs === 0) {
                $status = 'not_configured';
                $statusText = 'Non configuré';
                $statusClass = 'text-secondary';
                $statusIcon = 'fa-circle';
            } elseif ($errorSyncs > 0 && $successfulSyncs === 0) {
                $status = 'error';
                $statusText = 'Erreur';
                $statusClass = 'text-danger';
                $statusIcon = 'fa-exclamation-circle';
            } elseif ($successRate < 95) {
                $status = 'latency';
                $statusText = 'Latence';
                $statusClass = 'text-warning';
                $statusIcon = 'fa-triangle-exclamation';
            }
            
            $platformStats[$platform] = [
                'listings' => $totalSyncs,
                'success_rate' => $successRate,
                'status' => $status,
                'status_text' => $statusText,
                'status_class' => $statusClass,
                'status_icon' => $statusIcon,
                'last_sync' => $lastSync ? $lastSync->last_sync_at : null,
            ];
        }
        
        // Dernière mise à jour globale
        $lastGlobalUpdate = VillaIcalConfig::whereNotNull('last_sync_at')
            ->orderByDesc('last_sync_at')
            ->first();
        
        // Logs récents (dernières 10 synchronisations) - Utiliser PlatformSync pour l'historique
        $recentLogs = PlatformSync::with('villa')
            ->orderByDesc('last_sync_at')
            ->limit(10)
            ->get();
        
        // Statistiques pour le graphique (24h par défaut)
        $chartData = $this->getChartData('24h');
        
        return view('pages.admin.synchronization', compact('platformStats', 'lastGlobalUpdate', 'recentLogs', 'chartData'));
    }

    /**
     * Récupère les données pour le graphique selon la période
     */
    private function getChartData(string $period = '24h'): array
    {
        $now = now();
        $startDate = match($period) {
            '24h' => $now->copy()->subHours(24),
            '7j' => $now->copy()->subDays(7),
            '30j' => $now->copy()->subDays(30),
            default => $now->copy()->subHours(24),
        };

        // Récupérer toutes les synchronisations dans la période depuis PlatformSync
        $syncs = PlatformSync::where('last_sync_at', '>=', $startDate)
            ->orderBy('last_sync_at')
            ->get();

        // Grouper par période selon le type
        $groupedSyncs = [];
        $groupedErrors = [];

        if ($period === '24h') {
            // Grouper par heure
            for ($i = 0; $i < 24; $i++) {
                $hour = $now->copy()->subHours(23 - $i);
                $hourKey = $hour->format('H:00');
                
                $hourSyncs = $syncs->filter(function($sync) use ($hour) {
                    return $sync->last_sync_at->format('Y-m-d H') === $hour->format('Y-m-d H');
                });
                
                $groupedSyncs[$hourKey] = $hourSyncs->count();
                $groupedErrors[$hourKey] = $hourSyncs->where('status', 'error')->count();
            }
        } else {
            // Grouper par jour
            $days = $period === '7j' ? 7 : 30;
            for ($i = 0; $i < $days; $i++) {
                $day = $now->copy()->subDays($days - 1 - $i);
                $dayKey = $day->format('d/m');
                
                $daySyncs = $syncs->filter(function($sync) use ($day) {
                    return $sync->last_sync_at->format('Y-m-d') === $day->format('Y-m-d');
                });
                
                $groupedSyncs[$dayKey] = $daySyncs->count();
                $groupedErrors[$dayKey] = $daySyncs->where('status', 'error')->count();
            }
        }

        return [
            'labels' => array_keys($groupedSyncs),
            'syncs' => array_values($groupedSyncs),
            'errors' => array_values($groupedErrors),
        ];
    }

    /**
     * API pour récupérer les données du graphique selon la période
     */
    public function getChartDataApi(Request $request)
    {
        $period = $request->get('period', '24h');
        $chartData = $this->getChartData($period);
        
        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    /**
     * Génère le fichier iCal pour une villa
     */
    public function exportIcal($villaId)
    {
        $villa = Villa::findOrFail($villaId);
        $ical = $this->icalService->generateIcalForVilla($villa);

        return response($ical, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="reservations-' . $villa->id . '.ics"');
    }

    /**
     * Force la synchronisation depuis toutes les plateformes configurées
     */
    public function forceSync(Request $request)
    {
        try {
            $configs = VillaIcalConfig::where('is_active', true)
                ->whereNotNull('ical_import_url')
                ->with('villa')
                ->get();

            $results = [
                'success' => 0,
                'errors' => 0,
                'details' => []
            ];

            foreach ($configs as $config) {
                $syncResult = $this->icalService->syncVillaFromPlatform($config);
                
                if ($syncResult['success']) {
                    $results['success']++;
                    $results['details'][] = [
                        'villa' => $config->villa->name,
                        'platform' => $config->platform_name ?? ucfirst($config->platform),
                        'status' => 'success',
                        'message' => $syncResult['message']
                    ];
                } else {
                    $results['errors']++;
                    $results['details'][] = [
                        'villa' => $config->villa->name,
                        'platform' => $config->platform_name ?? ucfirst($config->platform),
                        'status' => 'error',
                        'message' => $syncResult['message']
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation terminée',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la synchronisation forcée", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Enregistre ou met à jour une configuration iCal
     */
    public function storeConfig(Request $request)
    {
        $validated = $request->validate([
            'villa_id' => 'required|exists:villas,id',
            'platform' => 'required|in:airbnb,booking,vrbo,abritel',
            'ical_export_url' => 'nullable|url',
            'ical_import_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        // Générer automatiquement l'URL d'export si elle n'est pas fournie
        if (empty($validated['ical_export_url'])) {
            $validated['ical_export_url'] = route('admin.synchronization.ical.export', $validated['villa_id']);
        }

        $config = VillaIcalConfig::updateOrCreate(
            [
                'villa_id' => $validated['villa_id'],
                'platform' => $validated['platform'],
            ],
            [
                'ical_export_url' => $validated['ical_export_url'],
                'ical_import_url' => $validated['ical_import_url'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuration enregistrée avec succès',
            'config' => $config->load('villa')
        ]);
    }

    /**
     * Supprime une configuration iCal
     */
    public function deleteConfig($id)
    {
        $config = VillaIcalConfig::findOrFail($id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => 'Configuration supprimée avec succès'
        ]);
    }

    /**
     * Affiche une configuration iCal pour édition
     */
    public function showConfig($id)
    {
        $config = VillaIcalConfig::with('villa')->findOrFail($id);

        return response()->json([
            'success' => true,
            'config' => $config
        ]);
    }

    /**
     * Affiche la page de configuration iCal
     */
    public function config()
    {
        $villas = Villa::where('is_active', true)->orderBy('name')->get();
        $configs = VillaIcalConfig::with('villa')->get()->groupBy('villa_id');
        
        return view('pages.admin.synchronization-config', compact('villas', 'configs'));
    }
}
