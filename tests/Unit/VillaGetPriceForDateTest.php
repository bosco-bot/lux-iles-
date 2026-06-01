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
                ['price' => 300, 'season' => $this->season(1, 1, 12, 31)],
                ['price' => 500, 'season' => $this->season(6, 1, 8, 31)],
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
                ['price' => 400, 'season' => $this->season(12, 1, 12, 31)],
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
                ['price' => 275, 'season' => $this->season(4, 1, 5, 15)],
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
                ['price' => 200, 'season' => $this->season(1, 1, 12, 31)],
                ['price' => 400, 'season' => $this->season(7, 1, 7, 31)],
            ]
        );

        // 2 nuits en juillet : 400 + 400 = 800 (pas 200)
        $total = $villa->calculatePriceForPeriod('2025-07-10', '2025-07-12');

        $this->assertSame(800.0, $total);
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

    private function season(int $startMonth, int $startDay, int $endMonth, int $endDay): Season
    {
        return new Season([
            'name' => 'Test season',
            'start_month' => $startMonth,
            'start_day' => $startDay,
            'end_month' => $endMonth,
            'end_day' => $endDay,
            'is_active' => true,
        ]);
    }
}
