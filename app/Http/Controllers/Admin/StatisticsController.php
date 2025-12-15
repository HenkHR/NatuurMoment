<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamePlayer;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    // ============================================
    // CONSTANTS
    // ============================================

    private const AGE_GROUPS = [
        '≤12' => [0, 12],
        '13-15' => [13, 15],
        '16-18' => [16, 18],
        '19-21' => [19, 21],
        '22+' => [22, 999],
    ];

    private const AGE_GROUP_LABELS = ['≤12', '13-15', '16-18', '19-21', '22+'];

    // ============================================
    // MAIN ACTION
    // ============================================

    /**
     * Display the statistics dashboard
     * REQ-002: Statistieken dashboard pagina beschikbaar in admin panel
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');

        // Check if we have any feedback data
        $hasFeedback = GamePlayer::whereNotNull('feedback_rating')->exists();

        if (!$hasFeedback) {
            return view('admin.statistics.index', [
                'hasFeedback' => false,
                'stats' => null,
                'ageDistribution' => null,
                'satisfactionByAge' => null,
                'trendsData' => null,
                'ratingByLocation' => null,
                'period' => $period,
            ]);
        }

        return view('admin.statistics.index', [
            'hasFeedback' => true,
            'stats' => $this->getOverviewStats(),
            'ageDistribution' => $this->getAgeDistribution(),
            'satisfactionByAge' => $this->getSatisfactionByAge(),
            'trendsData' => $this->getTrends($period),
            'ratingByLocation' => $this->getRatingByLocation(),
            'period' => $period,
        ]);
    }

    /**
     * Return trends data as JSON for AJAX filter
     * REQ-006: Lijndiagram toont trends met dropdown filter
     */
    public function trends(Request $request)
    {
        $period = $request->get('period', 'month');

        return response()->json($this->getTrends($period));
    }

    // ============================================
    // QUERY METHODS
    // ============================================

    /**
     * Get overview stats for stat cards
     * REQ-003: 4 stat cards tonen
     */
    private function getOverviewStats(): array
    {
        // Total responses
        $totalResponses = GamePlayer::whereNotNull('feedback_rating')->count();

        // Average rating
        $averageRating = GamePlayer::whereNotNull('feedback_rating')
            ->avg('feedback_rating');

        // Responses this month
        $responsesThisMonth = GamePlayer::whereNotNull('feedback_rating')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Most active location
        $mostActiveLocation = GamePlayer::query()
            ->join('games', 'game_players.game_id', '=', 'games.id')
            ->join('locations', 'games.location_id', '=', 'locations.id')
            ->whereNotNull('game_players.feedback_rating')
            ->select('locations.name', DB::raw('COUNT(*) as response_count'))
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('response_count')
            ->first();

        return [
            'total_responses' => $totalResponses,
            'average_rating' => $averageRating ? round($averageRating, 1) : null,
            'responses_this_month' => $responsesThisMonth,
            'most_active_location' => $mostActiveLocation?->name ?? 'N/A',
            'most_active_location_count' => $mostActiveLocation?->response_count ?? 0,
        ];
    }

    /**
     * Get age distribution data for bar chart
     * REQ-004: Staafdiagram toont leeftijdsverdeling
     * REQ-009: Leeftijd wordt gecategoriseerd in 5 groepen
     */
    private function getAgeDistribution(): array
    {
        $castType = $this->getIntegerCastType();

        $results = GamePlayer::query()
            ->selectRaw("
                CASE
                    WHEN CAST(feedback_age AS {$castType}) <= 12 THEN '≤12'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 13 AND 15 THEN '13-15'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 16 AND 18 THEN '16-18'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 19 AND 21 THEN '19-21'
                    ELSE '22+'
                END as age_group,
                COUNT(*) as count
            ")
            ->whereNotNull('feedback_age')
            ->where('feedback_age', '!=', '')
            ->groupBy('age_group')
            ->get()
            ->keyBy('age_group');

        // Ensure all categories present (even with 0 count)
        $data = [];
        foreach (self::AGE_GROUP_LABELS as $label) {
            $data[] = $results->get($label)?->count ?? 0;
        }

        return [
            'labels' => self::AGE_GROUP_LABELS,
            'data' => $data,
        ];
    }

    /**
     * Get satisfaction by age group for grouped bar chart
     * REQ-005: Grouped staafdiagram toont tevredenheid per leeftijdscategorie
     */
    private function getSatisfactionByAge(): array
    {
        $castType = $this->getIntegerCastType();

        $results = GamePlayer::query()
            ->selectRaw("
                CASE
                    WHEN CAST(feedback_age AS {$castType}) <= 12 THEN '≤12'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 13 AND 15 THEN '13-15'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 16 AND 18 THEN '16-18'
                    WHEN CAST(feedback_age AS {$castType}) BETWEEN 19 AND 21 THEN '19-21'
                    ELSE '22+'
                END as age_group,
                AVG(feedback_rating) as avg_rating,
                COUNT(*) as count
            ")
            ->whereNotNull('feedback_age')
            ->whereNotNull('feedback_rating')
            ->where('feedback_age', '!=', '')
            ->groupBy('age_group')
            ->get()
            ->keyBy('age_group');

        // Ensure all categories present
        $avgRatings = [];
        $counts = [];
        foreach (self::AGE_GROUP_LABELS as $label) {
            $avgRatings[] = $results->get($label) ? round($results->get($label)->avg_rating, 1) : 0;
            $counts[] = $results->get($label)?->count ?? 0;
        }

        return [
            'labels' => self::AGE_GROUP_LABELS,
            'avgRatings' => $avgRatings,
            'counts' => $counts,
        ];
    }

    /**
     * Get trends data for line chart
     * REQ-006: Lijndiagram toont trends met dropdown filter
     * REQ-010: Aggregatie queries berekenen AVG rating, GROUP BY tijd
     */
    private function getTrends(string $period): array
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        // Date format differs between SQLite and MySQL
        if ($isSqlite) {
            $format = match ($period) {
                'week' => '%Y-%W',
                'month' => '%Y-%m',
                'year' => '%Y',
                default => '%Y-%m',
            };
            $periodSelect = "strftime('{$format}', created_at)";
        } else {
            // MySQL uses DATE_FORMAT
            $format = match ($period) {
                'week' => '%Y-%u',
                'month' => '%Y-%m',
                'year' => '%Y',
                default => '%Y-%m',
            };
            $periodSelect = "DATE_FORMAT(created_at, '{$format}')";
        }

        $results = GamePlayer::query()
            ->selectRaw("
                {$periodSelect} as period,
                AVG(feedback_rating) as avg_rating,
                COUNT(*) as count
            ")
            ->whereNotNull('feedback_rating')
            ->groupByRaw($periodSelect)
            ->orderBy('period')
            ->get();

        // Format labels for display
        $labels = $results->map(function ($item) use ($period) {
            // Parse period back to readable format
            if ($period === 'week') {
                // Format: YYYY-WW
                [$year, $week] = explode('-', $item->period);
                return "Week {$week}, {$year}";
            } elseif ($period === 'month') {
                // Format: YYYY-MM
                $date = \Carbon\Carbon::createFromFormat('Y-m', $item->period);
                return $date->translatedFormat('M Y');
            } else {
                // Format: YYYY
                return $item->period;
            }
        })->toArray();

        return [
            'labels' => $labels,
            'avgRatings' => $results->pluck('avg_rating')->map(fn($r) => round($r, 1))->toArray(),
            'counts' => $results->pluck('count')->toArray(),
            'period' => $period,
        ];
    }

    /**
     * Get rating by location for horizontal bar chart
     * REQ-007: Horizontaal staafdiagram toont gemiddelde rating per locatie
     */
    private function getRatingByLocation(): array
    {
        $results = GamePlayer::query()
            ->join('games', 'game_players.game_id', '=', 'games.id')
            ->join('locations', 'games.location_id', '=', 'locations.id')
            ->selectRaw('
                locations.name,
                AVG(game_players.feedback_rating) as avg_rating,
                COUNT(*) as count
            ')
            ->whereNotNull('game_players.feedback_rating')
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('avg_rating')
            ->limit(10)
            ->get();

        return [
            'labels' => $results->pluck('name')->toArray(),
            'avgRatings' => $results->pluck('avg_rating')->map(fn($r) => round($r, 1))->toArray(),
            'counts' => $results->pluck('count')->toArray(),
        ];
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get the correct SQL cast type for integers based on database driver.
     * SQLite uses INTEGER, MySQL uses SIGNED.
     */
    private function getIntegerCastType(): string
    {
        return DB::connection()->getDriverName() === 'sqlite' ? 'INTEGER' : 'SIGNED';
    }
}
