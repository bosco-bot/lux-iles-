<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\User;
use App\Models\PlatformSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord avec les statistiques
     */
    public function index()
    {
        try {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
            $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();
            
            // KPIs - Revenus du mois
            $currentMonthRevenue = 0;
            if (DB::getSchemaBuilder()->hasTable('payments')) {
                $currentMonthRevenue = Payment::where('status', 'completed')
                    ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount') ?? 0;
            }
        
            $lastMonthRevenue = 0;
            if (DB::getSchemaBuilder()->hasTable('payments')) {
                $lastMonthRevenue = Payment::where('status', 'completed')
                    ->whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])
                    ->sum('amount') ?? 0;
            }
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;
        
            // KPIs - Taux d'occupation
            $totalVillas = Villa::where('is_active', true)->count();
            $totalNightsThisMonth = 0;
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $totalNightsThisMonth = Reservation::whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'completed'])
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('check_in_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('check_out_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('check_in_date', '<=', $startOfMonth)
                            ->where('check_out_date', '>=', $endOfMonth);
                      });
                })
                ->sum('number_of_nights') ?? 0;
            }
            
            $possibleNights = $totalVillas * $endOfMonth->diffInDays($startOfMonth);
            $occupancyRate = $possibleNights > 0 ? ($totalNightsThisMonth / $possibleNights) * 100 : 0;
            
            $totalNightsLastMonth = 0;
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $totalNightsLastMonth = Reservation::whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'completed'])
            ->where(function($query) use ($startOfLastMonth, $endOfLastMonth) {
                $query->whereBetween('check_in_date', [$startOfLastMonth, $endOfLastMonth])
                      ->orWhereBetween('check_out_date', [$startOfLastMonth, $endOfLastMonth])
                      ->orWhere(function($q) use ($startOfLastMonth, $endOfLastMonth) {
                          $q->where('check_in_date', '<=', $startOfLastMonth)
                            ->where('check_out_date', '>=', $endOfLastMonth);
                      });
                })
                ->sum('number_of_nights') ?? 0;
            }
            
            $possibleNightsLastMonth = $totalVillas * $endOfLastMonth->diffInDays($startOfLastMonth);
            $occupancyRateLastMonth = $possibleNightsLastMonth > 0 ? ($totalNightsLastMonth / $possibleNightsLastMonth) * 100 : 0;
            $occupancyGrowth = $occupancyRateLastMonth > 0 
                ? (($occupancyRate - $occupancyRateLastMonth) / $occupancyRateLastMonth) * 100 
                : 0;
            
            // KPIs - Réservations du mois
            $currentMonthBookings = 0;
            $lastMonthBookings = 0;
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $currentMonthBookings = Reservation::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->whereIn('status', ['pending', 'confirmed', 'deposit_paid', 'fully_paid'])
                    ->count();
                
                $lastMonthBookings = Reservation::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->whereIn('status', ['pending', 'confirmed', 'deposit_paid', 'fully_paid'])
                    ->count();
            }
            
            // KPIs - Actions à traiter
            $pendingActions = 0;
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $pendingActions = Reservation::whereIn('status', ['pending'])
                    ->count();
            }
        
            // Données pour les graphiques (12 derniers mois)
            $chartData = [];
            $revenueData = [];
            $occupancyData = [];
            $months = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $monthStart = $now->copy()->subMonths($i)->startOfMonth();
                $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
                
                $monthRevenue = 0;
                if (DB::getSchemaBuilder()->hasTable('payments')) {
                    $monthRevenue = Payment::where('status', 'completed')
                        ->whereBetween('paid_at', [$monthStart, $monthEnd])
                        ->sum('amount') ?? 0;
                }
                
                $monthNights = 0;
                if (DB::getSchemaBuilder()->hasTable('reservations')) {
                    $monthNights = Reservation::whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'completed'])
                        ->where(function($query) use ($monthStart, $monthEnd) {
                            $query->whereBetween('check_in_date', [$monthStart, $monthEnd])
                                  ->orWhereBetween('check_out_date', [$monthStart, $monthEnd])
                                  ->orWhere(function($q) use ($monthStart, $monthEnd) {
                                      $q->where('check_in_date', '<=', $monthStart)
                                        ->where('check_out_date', '>=', $monthEnd);
                                  });
                        })
                        ->sum('number_of_nights') ?? 0;
                }
                
                $possibleNightsMonth = $totalVillas * $monthEnd->diffInDays($monthStart);
                $monthOccupancy = $possibleNightsMonth > 0 ? ($monthNights / $possibleNightsMonth) * 100 : 0;
                
                $revenueData[] = round($monthRevenue, 0);
                $occupancyData[] = round($monthOccupancy, 1);
                $months[] = $monthStart->format('M');
            }
            
            // Données pour les graphiques (12 dernières années)
            $revenueDataYear = [];
            $occupancyDataYear = [];
            $years = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $yearStart = $now->copy()->subYears($i)->startOfYear();
                $yearEnd = $now->copy()->subYears($i)->endOfYear();
                
                $yearRevenue = 0;
                if (DB::getSchemaBuilder()->hasTable('payments')) {
                    $yearRevenue = Payment::where('status', 'completed')
                        ->whereBetween('paid_at', [$yearStart, $yearEnd])
                        ->sum('amount') ?? 0;
                }
                
                $yearNights = 0;
                if (DB::getSchemaBuilder()->hasTable('reservations')) {
                    $yearNights = Reservation::whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'completed'])
                        ->where(function($query) use ($yearStart, $yearEnd) {
                            $query->whereBetween('check_in_date', [$yearStart, $yearEnd])
                                  ->orWhereBetween('check_out_date', [$yearStart, $yearEnd])
                                  ->orWhere(function($q) use ($yearStart, $yearEnd) {
                                      $q->where('check_in_date', '<=', $yearStart)
                                        ->where('check_out_date', '>=', $yearEnd);
                                  });
                        })
                        ->sum('number_of_nights') ?? 0;
                }
                
                $possibleNightsYear = $totalVillas * $yearEnd->diffInDays($yearStart);
                $yearOccupancy = $possibleNightsYear > 0 ? ($yearNights / $possibleNightsYear) * 100 : 0;
                
                $revenueDataYear[] = round($yearRevenue, 0);
                $occupancyDataYear[] = round($yearOccupancy, 1);
                $years[] = $yearStart->format('Y');
            }
            
            // Répartition des réservations (Direct vs Partenaires)
            $directReservations = 0;
            $partnerReservations = 0;
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $directReservations = Reservation::where('source', 'direct')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
                
                $partnerReservations = Reservation::whereIn('source', ['airbnb', 'booking', 'abritel'])
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
            }
            
            $totalReservations = $directReservations + $partnerReservations;
            $directPercentage = $totalReservations > 0 ? ($directReservations / $totalReservations) * 100 : 0;
            $partnerPercentage = $totalReservations > 0 ? ($partnerReservations / $totalReservations) * 100 : 0;
            
            // Dernières réservations
            $recentReservations = collect([]);
            if (DB::getSchemaBuilder()->hasTable('reservations')) {
                $recentReservations = Reservation::with(['villa'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
            
            // Alertes de synchronisation
            $syncAlerts = collect([]);
            $lastSync = null;
            if (DB::getSchemaBuilder()->hasTable('platform_syncs')) {
                $syncAlerts = PlatformSync::with('villa')
                    ->whereIn('status', ['error', 'conflict'])
                    ->where('last_sync_at', '>=', $now->copy()->subDays(7))
                    ->orderByDesc('updated_at')
                    ->limit(5)
                    ->get();
                
                // Dernière synchronisation
                $lastSync = PlatformSync::where('status', 'synced')
                    ->orderByDesc('last_sync_at')
                    ->first();
            }
            
            return view('pages.admin.dashboard', compact(
                'currentMonthRevenue',
                'revenueGrowth',
                'occupancyRate',
                'occupancyGrowth',
                'currentMonthBookings',
                'lastMonthBookings',
                'pendingActions',
                'revenueData',
                'occupancyData',
                'months',
                'revenueDataYear',
                'occupancyDataYear',
                'years',
                'directPercentage',
                'partnerPercentage',
                'recentReservations',
                'syncAlerts',
                'lastSync'
            ));
        } catch (\Exception $e) {
            // En cas d'erreur (tables non créées), retourner des valeurs par défaut
            return view('pages.admin.dashboard', [
                'currentMonthRevenue' => 0,
                'revenueGrowth' => 0,
                'occupancyRate' => 0,
                'occupancyGrowth' => 0,
                'currentMonthBookings' => 0,
                'lastMonthBookings' => 0,
                'pendingActions' => 0,
                'revenueData' => array_fill(0, 12, 0),
                'occupancyData' => array_fill(0, 12, 0),
                'months' => ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aout', 'Sep', 'Oct', 'Nov', 'Dec'],
                'revenueDataYear' => array_fill(0, 12, 0),
                'occupancyDataYear' => array_fill(0, 12, 0),
                'years' => array_map(function($i) use ($now) {
                    return $now->copy()->subYears(11 - $i)->format('Y');
                }, range(0, 11)),
                'directPercentage' => 0,
                'partnerPercentage' => 0,
                'recentReservations' => collect([]),
                'syncAlerts' => collect([]),
                'lastSync' => null,
            ]);
        }
    }
}

