
<x-app-layout>

    <div class="min-h-screen bg-slate-900 text-white p-8">

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

    <!-- Temperature -->

    <div class="bg-slate-800/90 backdrop-blur-lg p-5 rounded-2xl shadow-lg">

        <p class="text-slate-400 text-sm">
            Temperature
        </p>

        <h2 class="text-3xl font-bold text-cyan-400 mt-2">
            {{ round($weather['main']['temp']) }}°C
        </h2>

    </div>

    <!-- Weather Condition -->

    <div class="bg-slate-800/90 backdrop-blur-lg p-5 rounded-2xl shadow-lg">

        <p class="text-slate-400 text-sm">
            Condition
        </p>

        <h2 class="text-2xl font-bold text-yellow-400 mt-2">
            {{ $weather['weather'][0]['main'] }}
        </h2>

    </div>

    <!-- Humidity -->

    <div class="bg-slate-800/90 backdrop-blur-lg p-5 rounded-2xl shadow-lg">

        <p class="text-slate-400 text-sm">
            Humidity
        </p>

        <h2 class="text-3xl font-bold text-purple-400 mt-2">
            {{ $weather['main']['humidity'] }}%
        </h2>

    </div>

    <!-- Wind -->

    <div class="bg-slate-800/90 backdrop-blur-lg p-5 rounded-2xl shadow-lg">

        <p class="text-slate-400 text-sm">
            Wind Speed
        </p>

        <h2 class="text-3xl font-bold text-green-400 mt-2">
            {{ round($weather['wind']['speed']) }}
        </h2>

    </div>

</div>



        <h1 class="text-4xl font-bold mb-8">
            City Movement Map
        </h1>

        <!-- Live Activity Feed -->

        <div class="bg-slate-800 rounded-2xl shadow-lg p-6 mb-6">

            <h2 class="text-2xl font-semibold mb-4">
                Live Movement Feed
            </h2>

            <div
                id="activityFeed"
                class="space-y-3 max-h-64 overflow-y-auto"
            >
            </div>

        </div>

        <!-- Map -->

        <div
            id="map"
            class="rounded-2xl overflow-hidden shadow-lg"
        ></div>

    </div>

    <style>

.rain {

    position: fixed;

    width: 2px;

    height: 100px;

    background: rgba(255,255,255,0.2);

    animation: rainFall linear infinite;
}

@keyframes rainFall {

    from {
        transform: translateY(-100px);
    }

    to {
        transform: translateY(100vh);
    }
}
        #map {
            height: 700px;
            width: 100%;
        }

        .leaflet-popup-content-wrapper {
            background: #1e293b;
            color: white;
            border-radius: 12px;
        }

        .leaflet-popup-tip {
            background: #1e293b;
        }

    </style>

    <!-- Leaflet CSS -->

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    <!-- Leaflet JS -->

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>

        // Initialize Map

        const map = L.map('map').setView([30.7333, 76.7794], 13);

        // OpenStreetMap Tiles

       L.tileLayer(
    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
    {
        attribution:
            '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }
).addTo(map);
        // Laravel Zones Data

        const zones = @json($zones);

        const weatherCondition =
    @json($weather['weather'][0]['main']);

    if (weatherCondition === 'Rain') {

    document.body.style.background =
        '#0f172a';

}

if (weatherCondition === 'Clear') {

    document.body.style.background =
        'linear-gradient(to bottom, #0f172a, #111827)';
}


zones.forEach(zone => {

    const marker = L.circleMarker(
        [zone.latitude, zone.longitude],
        {
            radius: 7,
            opacity: 1,
            color: '#38bdf8',
            fillColor: '#38bdf8',
            fillOpacity: 1,
            weight: 2
        }
    ).addTo(map);

    marker.bindPopup(`
        <div style="min-width: 200px;">
            <h3 style="font-size:18px; margin-bottom:10px;">
                ${zone.name}
            </h3>

            <p>
                Population:
                <b>${zone.population}</b>
            </p>

            <p>
                Traffic:
                <b>${zone.traffic_level}%</b>
            </p>
        </div>
    `);

});

        // Activity Feed Element

        const activityFeed = document.getElementById('activityFeed');

        // Load Live Movement Simulation

        async function loadMovements() {

            const response = await fetch('/movement-data');

            const movements = await response.json();

            movements.forEach(movement => {

                // Draw Animated Route Line

                const polyline = L.polyline([
                    [movement.from.lat, movement.from.lng],
                    [movement.to.lat, movement.to.lng]
                ], {
                    color: '#38bdf8',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '10, 10'
                }).addTo(map);

                // Create Moving Dot

                const movingCircle = L.circleMarker(
                    [movement.from.lat, movement.from.lng],
                    {
                        radius: 8,
                        color: '#ffffff',
                        fillColor: '#38bdf8',
                        fillOpacity: 1
                    }
                ).addTo(map);

                // Add Activity Feed Entry

                const activity = document.createElement('div');

                activity.className =
                    'bg-slate-700 p-3 rounded-xl text-sm';

                activity.innerHTML = `
                    <span class="text-cyan-400 font-semibold">
                        ${movement.people} people
                    </span>

                    moved from

                    <span class="text-purple-400">
                        ${movement.from.name}
                    </span>

                    to

                    <span class="text-green-400">
                        ${movement.to.name}
                    </span>

                    via

                    <span class="text-yellow-400">
                        ${movement.transport}
                    </span>
                `;

                activityFeed.prepend(activity);

                // Keep Feed Short

                if (activityFeed.children.length > 10) {
                    activityFeed.removeChild(
                        activityFeed.lastChild
                    );
                }

                // Animation Logic

                let progress = 0;

                const animation = setInterval(() => {

                    progress += 0.01;

                    const lat =
                        movement.from.lat +
                        (
                            movement.to.lat -
                            movement.from.lat
                        ) * progress;

                    const lng =
                        movement.from.lng +
                        (
                            movement.to.lng -
                            movement.from.lng
                        ) * progress;

                    movingCircle.setLatLng([lat, lng]);

                    // Remove After Reaching Destination

                    if (progress >= 1) {

                        clearInterval(animation);

                        map.removeLayer(movingCircle);

                        map.removeLayer(polyline);
                    }

                }, 30);

            });

        }

        // Initial Load

        loadMovements();

        // Repeat Every 5 Seconds

        setInterval(() => {

            loadMovements();

        }, 5000);

        if (weatherCondition === 'Rain') {

    for (let i = 0; i < 100; i++) {

        const rain = document.createElement('div');

        rain.classList.add('rain');

        rain.style.left =
            Math.random() * 100 + 'vw';

        rain.style.animationDuration =
            (Math.random() * 1 + 0.5) + 's';

        rain.style.opacity =
            Math.random();

        document.body.appendChild(rain);
    }
}

    </script>

</x-app-layout>

