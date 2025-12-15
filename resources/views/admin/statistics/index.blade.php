<x-admin.layout>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-h2 text-deep-black">Statistieken</h2>
    </div>

    @if(!$hasFeedback)
        {{-- REQ-011: Dashboard toont lege staat message als geen feedback data aanwezig --}}
        <div class="bg-pure-white rounded-card shadow-card p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Geen feedback gegevens</h3>
            <p class="text-gray-500">Er is nog geen feedback data beschikbaar. Statistieken verschijnen zodra spelers feedback geven na het spelen.</p>
        </div>
    @else
        {{-- REQ-003: 4 stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Total Responses --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Totaal Responses</p>
                        <p class="text-3xl font-bold text-forest-700 mt-1">{{ number_format($stats['total_responses']) }}</p>
                    </div>
                    <div class="p-3 bg-forest-100 rounded-full">
                        <svg class="w-6 h-6 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Responses This Month --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Deze Maand</p>
                        <p class="text-3xl font-bold text-forest-700 mt-1">{{ number_format($stats['responses_this_month']) }}</p>
                    </div>
                    <div class="p-3 bg-sky-100 rounded-full">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Average Rating --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Gemiddelde Rating</p>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-3xl font-bold text-forest-700">{{ $stats['average_rating'] ?? 'N/A' }}</p>
                            @if($stats['average_rating'])
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($stats['average_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Most Active Location --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Meest Actieve Locatie</p>
                        <p class="text-lg font-bold text-forest-700 mt-1 truncate" title="{{ $stats['most_active_location'] }}">{{ $stats['most_active_location'] }}</p>
                        <p class="text-sm text-gray-400">{{ $stats['most_active_location_count'] }} responses</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- REQ-004: Age Distribution Chart --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <h3 class="text-lg font-semibold text-deep-black mb-4">Leeftijdsverdeling</h3>
                <div style="height: 300px;">
                    <canvas id="ageDistributionChart"></canvas>
                </div>
            </div>

            {{-- REQ-005: Satisfaction by Age Chart --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <h3 class="text-lg font-semibold text-deep-black mb-4">Tevredenheid per Leeftijd</h3>
                <div style="height: 300px;">
                    <canvas id="satisfactionByAgeChart"></canvas>
                </div>
            </div>

            {{-- REQ-006: Trends Chart with AJAX Filter --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-deep-black">Trends Over Tijd</h3>
                    <select
                        id="trendPeriodSelect"
                        class="rounded-lg border border-gray-300 pl-3 pr-8 py-1.5 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 appearance-none bg-no-repeat bg-right bg-[length:1.25rem]"
                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 0.5rem center;"
                    >
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Per Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Per Maand</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Per Jaar</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>

            {{-- REQ-007: Rating by Location Chart --}}
            <div class="bg-pure-white rounded-card shadow-card p-6">
                <h3 class="text-lg font-semibold text-deep-black mb-4">Gemiddelde Rating per Locatie</h3>
                <div style="height: 300px;">
                    <canvas id="ratingByLocationChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Chart defaults
                Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
                Chart.defaults.color = '#374151';

                const forestColor = '#15803D';
                const skyColor = '#0EA5E9';
                const yellowColor = '#EAB308';
                const orangeColor = '#F97316';

                // REQ-004: Age Distribution Bar Chart
                const ageDistCtx = document.getElementById('ageDistributionChart').getContext('2d');
                new Chart(ageDistCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($ageDistribution['labels']),
                        datasets: [{
                            label: 'Aantal Spelers',
                            data: @json($ageDistribution['data']),
                            backgroundColor: forestColor,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });

                // REQ-005: Satisfaction by Age Grouped Bar Chart
                const satByAgeCtx = document.getElementById('satisfactionByAgeChart').getContext('2d');
                new Chart(satByAgeCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($satisfactionByAge['labels']),
                        datasets: [{
                            label: 'Gem. Rating',
                            data: @json($satisfactionByAge['avgRatings']),
                            backgroundColor: yellowColor,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 5,
                                ticks: {
                                    stepSize: 0.5,
                                    callback: function(value) {
                                        // Alleen hele getallen tonen als label
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        }
                    }
                });

                // REQ-006: Trends Line Chart
                const trendsCtx = document.getElementById('trendsChart').getContext('2d');
                let trendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trendsData['labels']),
                        datasets: [{
                            label: 'Gem. Rating',
                            data: @json($trendsData['avgRatings']),
                            borderColor: skyColor,
                            backgroundColor: skyColor + '20',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 5,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });

                // AJAX filter for trends
                document.getElementById('trendPeriodSelect').addEventListener('change', async function() {
                    const period = this.value;
                    try {
                        const response = await fetch(`{{ route('admin.statistics.trends') }}?period=${period}`);
                        const data = await response.json();

                        trendsChart.data.labels = data.labels;
                        trendsChart.data.datasets[0].data = data.avgRatings;
                        trendsChart.update();
                    } catch (error) {
                        console.error('Failed to fetch trends:', error);
                    }
                });

                // REQ-007: Rating by Location Horizontal Bar Chart
                const ratingByLocCtx = document.getElementById('ratingByLocationChart').getContext('2d');
                new Chart(ratingByLocCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($ratingByLocation['labels']),
                        datasets: [{
                            label: 'Gem. Rating',
                            data: @json($ratingByLocation['avgRatings']),
                            backgroundColor: orangeColor,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 5,
                                ticks: {
                                    stepSize: 0.5,
                                    callback: function(value) {
                                        // Alleen hele getallen tonen als label
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-admin.layout>
