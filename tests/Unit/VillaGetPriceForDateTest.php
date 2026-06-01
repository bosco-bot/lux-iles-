<?php

namespace Tests\Unit;

use App\Models\Season;
use App\Models\Villa;
use App\Models\VillaSeasonalPrice;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VillaGetPriceForDateTest extends TestCase
{
    #[Test]
    public function it_returns_highest_price_when_multiple_seasons_overlap(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 100,
            prices: [
                ['price' => 300, 'season' => $this->season('2025-01-01', '2025-12-31')],
                ['price' => 500, 'season' => $this->season('2025-06-01', '2025-08-31')],
            ]
        );

        $this->assertSame(500.0, $villa->getPriceForDate('2025-07-15'));
    }

    #[Test]
    public function it_returns_base_price_when_no_season_matches(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 150,
            prices: [
                ['price' => 400, 'season' => $this->season('2025-12-01', '2025-12-31')],
            ]
        );

        $this->assertSame(150.0, $villa->getPriceForDate('2025-06-15'));
    }

    #[Test]
    public function it_returns_single_season_price_when_only_one_matches(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 100,
            prices: [
                ['price' => 275, 'season' => $this->season('2025-04-01', '2025-05-15')],
            ]
        );

        $this->assertSame(275.0, $villa->getPriceForDate('2025-04-20'));
    }

    #[Test]
    public function calculate_price_for_period_sums_max_price_per_night(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 100,
            prices: [
                ['price' => 200, 'season' => $this->season('2025-01-01', '2025-12-31')],
                ['price' => 400, 'season' => $this->season('2025-07-01', '2025-07-31')],
            ]
        );

        $total = $villa->calculatePriceForPeriod('2025-07-10', '2025-07-12');

        $this->assertSame(800.0, $total);
    }

    #[Test]
    public function season_does_not_apply_outside_its_calendar_year(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 120,
            prices: [
                ['price' => 900, 'season' => $this->season('2025-07-01', '2025-07-31')],
            ]
        );

        $this->assertSame(120.0, $villa->getPriceForDate('2026-07-15'));
    }

    #[Test]
    public function inactive_season_is_ignored(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 100,
            prices: [
                ['price' => 999, 'season' => $this->season('2025-07-01', '2025-07-31', isActive: false)],
            ]
        );

        $this->assertSame(100.0, $villa->getPriceForDate('2025-07-15'));
    }

    #[Test]
    public function cross_year_season_period_is_supported(): void
    {
        $villa = $this->makeVillaWithSeasonalPrices(
            basePrice: 100,
            prices: [
                ['price' => 650, 'season' => $this->season('2025-12-20', '2026-01-05')],
            ]
        );

        $this->assertSame(650.0, $villa->getPriceForDate('2025-12-25'));
        $this->assertSame(650.0, $villa->getPriceForDate('2026-01-02'));
        $this->assertSame(100.0, $villa->getPriceForDate('2025-12-10'));
    }

    private function makeVillaWithSeasonalPrices(float $basePrice, array $prices): Villa
    {
        $villa = new Villa(['base_price_per_night' => $basePrice]);

        $seasonalPrices = collect($prices)->map(function (array $config) {
            $seasonalPrice = new VillaSeasonalPrice(['price_per_night' => $config['price']]);
            $seasonalPrice->setRelation('season', $config['season']);

            return $seasonalPrice;
        });

        $villa->setRelation('seasonalPrices', $seasonalPrices);

        return $villa;
    }

    private function season(string $startDate, string $endDate, bool $isActive = true): Season
    {
        return new Season([
            'name' => 'Test season',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $isActive,
        ]);
    }
}
