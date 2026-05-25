
<x-app-layout>

    <div class="min-h-screen bg-slate-900 text-white p-8">

        <!-- Page Heading -->

        <div class="flex items-center justify-between mb-8">

            <div>

                <h1 class="text-4xl font-bold">
                    CityFlow Dashboard
                </h1>

                <p class="text-slate-400 mt-2">
                    Real-Time Smart City Monitoring System
                </p>

            </div>

        </div>

        <!-- Stats Cards -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

            <!-- Citizens -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-lg text-slate-300">
                    Total Citizens
                </h2>

                <p class="text-4xl mt-4 font-bold text-cyan-400">

                    <span id="citizensCount">
                        {{ number_format($totalCitizens) }}
                    </span>

                </p>

            </div>

            <!-- Zones -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-lg text-slate-300">
                    Active Zones
                </h2>

                <p class="text-4xl mt-4 font-bold text-purple-400">

                    <span id="zonesCount">
                        {{ $activeZones }}
                    </span>

                </p>

            </div>

            <!-- Traffic -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-lg text-slate-300">
                    Average Traffic
                </h2>

                <p class="text-4xl mt-4 font-bold text-red-400">

                    <span id="trafficLevel">
                        {{ $averageTraffic }}
                    </span>%

                </p>

            </div>

            <!-- Weather -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-lg text-slate-300">
                    Live Weather
                </h2>

                <p class="text-4xl mt-4 font-bold text-cyan-400">

                    <span id="weatherTemp">
                        {{ round($weather['main']['temp']) }}
                    </span>°C

                </p>

                <p class="mt-2 text-slate-400">

                    <span id="weatherCondition">
                        {{ $weather['weather'][0]['main'] }}
                    </span>

                </p>

            </div>

        </div>

        <!-- Charts Section -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

            <!-- Traffic Chart -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-2xl font-semibold mb-4">
                    Traffic Levels
                </h2>

                <canvas id="trafficChart"></canvas>

            </div>

            <!-- Population Chart -->

            <div class="bg-slate-800 p-6 rounded-2xl shadow-lg">

                <h2 class="text-2xl font-semibold mb-4">
                    Population Distribution
                </h2>

                <canvas id="populationChart"></canvas>

            </div>

        </div>

        <!-- Live Activity Feed -->

        <div class="bg-slate-800 rounded-2xl shadow-lg p-6 mb-10">

            <h2 class="text-2xl font-semibold mb-4">
                Live Activity Feed
            </h2>

            <div
                id="activityFeed"
                class="space-y-3"
            >
            </div>

        </div>

        <!-- Zones Table -->

        <div class="bg-slate-800 rounded-2xl shadow-lg overflow-hidden">

            <div class="p-6 border-b border-slate-700">

                <h2 class="text-2xl font-semibold">
                    City Zones
                </h2>

            </div>

            <table class="w-full">

                <thead class="bg-slate-700 text-slate-300">

                    <tr>

                        <th class="text-left p-4">
                            Zone
                        </th>

                        <th class="text-left p-4">
                            Population
                        </th>

                        <th class="text-left p-4">
                            Traffic Level
                        </th>

                        <th class="text-left p-4">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody id="zonesTableBody">

                    @foreach($zones as $zone)

                    <tr class="border-b border-slate-700">

                        <td class="p-4">
                            {{ $zone->name }}
                        </td>

                        <td class="p-4">
                            {{ number_format($zone->population) }}
                        </td>

                        <td class="p-4">
                            {{ $zone->traffic_level }}%
                        </td>

                        <td class="p-4">

                            @if($zone->traffic_level >= 85)

                                <span class="text-red-400">
                                    Heavy
                                </span>

                            @elseif($zone->traffic_level >= 60)

                                <span class="text-yellow-400">
                                    Moderate
                                </span>

                            @else

                                <span class="text-green-400">
                                    Smooth
                                </span>

                            @endif

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <!-- Chart.js -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        // Initial Data

        const zoneNames =
            @json($zones->pluck('name'));

        const trafficLevels =
            @json($zones->pluck('traffic_level'));

        const populations =
            @json($zones->pluck('population'));

        // Traffic Chart

        const trafficChart = new Chart(

            document.getElementById(
                'trafficChart'
            ),

            {

                type: 'bar',

                data: {

                    labels: zoneNames,

                    datasets: [

                        {

                            label:
                                'Traffic Level',

                            data:
                                trafficLevels,

                            borderWidth: 1
                        }

                    ]
                },

                options: {

                    responsive: true,

                    scales: {

                        y: {

                            beginAtZero: true,

                            max: 100
                        }
                    }
                }
            }
        );

        // Population Chart

        const populationChart = new Chart(

            document.getElementById(
                'populationChart'
            ),

            {

                type: 'line',

                data: {

                    labels: zoneNames,

                    datasets: [

                        {

                            label:
                                'Population',

                            data:
                                populations,

                            borderWidth: 2,

                            tension: 0.4
                        }

                    ]
                },

                options: {

                    responsive: true
                }
            }
        );

        // Real-Time Dashboard Refresh

        async function refreshDashboard() {

            const response =
                await fetch(
                    '/dashboard-data'
                );

            const data =
                await response.json();

            // Update Cards

            document.getElementById(
                'citizensCount'
            ).innerText =
                data.citizens.toLocaleString();

            document.getElementById(
                'zonesCount'
            ).innerText =
                data.zones;

            document.getElementById(
                'trafficLevel'
            ).innerText =
                data.traffic;

            document.getElementById(
                'weatherTemp'
            ).innerText =
                data.weather.temp;

            document.getElementById(
                'weatherCondition'
            ).innerText =
                data.weather.condition;

            // Update Charts

            trafficChart.data.datasets[0].data =
                data.zonesData.map(
                    zone => zone.traffic_level
                );

            trafficChart.update();

            // Update Table

            const tableBody =
                document.getElementById(
                    'zonesTableBody'
                );

            tableBody.innerHTML = '';

            data.zonesData.forEach(zone => {

                let status =
                    'Smooth';

                let statusColor =
                    'text-green-400';

                if (
                    zone.traffic_level >= 85
                ) {

                    status =
                        'Heavy';

                    statusColor =
                        'text-red-400';
                }

                else if (
                    zone.traffic_level >= 60
                ) {

                    status =
                        'Moderate';

                    statusColor =
                        'text-yellow-400';
                }

                tableBody.innerHTML += `

                    <tr class="border-b border-slate-700">

                        <td class="p-4">
                            ${zone.name}
                        </td>

                        <td class="p-4">
                            ${zone.population}
                        </td>

                        <td class="p-4">
                            ${zone.traffic_level}%
                        </td>

                        <td class="p-4">

                            <span class="${statusColor}">
                                ${status}
                            </span>

                        </td>

                    </tr>
                `;
            });

            // Activity Feed

            const activityFeed =
                document.getElementById(
                    'activityFeed'
                );

            activityFeed.innerHTML = '';

            data.activities.forEach(activity => {

                const div =
                    document.createElement(
                        'div'
                    );

                div.className =
                    'bg-slate-700 p-3 rounded-xl';

                div.innerHTML = `

                    <div class="text-cyan-400">
                        ${activity.message}
                    </div>

                    <div class="text-slate-400 text-sm mt-1">
                        ${activity.time}
                    </div>
                `;

                activityFeed.appendChild(
                    div
                );

            });

        }

        // Initial Refresh

        refreshDashboard();

        // Auto Refresh Every 10 Seconds

        setInterval(() => {

            refreshDashboard();

        }, 10000);

    </script>

</x-app-layout>
