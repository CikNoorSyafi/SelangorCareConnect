@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endonce

<div class="mt-5 rounded-2xl border border-gray-200 bg-slate-50 p-4 md:p-5">
    <div class="mb-3 flex flex-col justify-between gap-2 md:flex-row md:items-center">
        <div>
            <h3 class="font-bold text-gray-900">Pin Campaign Location</h3>
            <p class="mt-1 text-sm text-gray-500">Search for the venue, then click the map to fine-tune the exact meeting point.</p>
        </div>
        <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
            <span class="h-2 w-2 rounded-full bg-red-500"></span> Map location required
        </span>
    </div>

    <div class="mb-3 flex flex-col gap-2 sm:flex-row">
        <input id="campaign-map-search" type="text" placeholder="Search venue, address or area in Selangor..."
            class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-100">
        <button type="button" id="campaign-map-search-button"
            class="rounded-lg bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">
            Find on map
        </button>
    </div>

    <div id="campaign-location-map" class="z-0 h-[360px] w-full overflow-hidden rounded-xl border border-gray-200"></div>

    <div class="mt-3 flex flex-col gap-1 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between">
        <span id="campaign-map-feedback">Click anywhere on the map to place your campaign pin.</span>
        <span id="campaign-map-coordinates" class="font-mono">No pin selected</span>
    </div>

    <input type="hidden" name="latitude" id="campaign-latitude" value="{{ old('latitude', $campaign->latitude ?? '') }}">
    <input type="hidden" name="longitude" id="campaign-longitude" value="{{ old('longitude', $campaign->longitude ?? '') }}">
    @error('latitude') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    @error('longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    <p class="mt-3 text-xs text-gray-400">Map data © OpenStreetMap contributors. The selected coordinates are saved with this campaign.</p>
</div>

<script>
(function () {
    const initCampaignLocationMap = () => {
        const mapElement = document.getElementById('campaign-location-map');
        if (!mapElement || mapElement.dataset.initialized === '1' || typeof L === 'undefined') return;
        mapElement.dataset.initialized = '1';

        const locationInput = document.getElementById('campaign-location');
        const latitudeInput = document.getElementById('campaign-latitude');
        const longitudeInput = document.getElementById('campaign-longitude');
        const searchInput = document.getElementById('campaign-map-search');
        const searchButton = document.getElementById('campaign-map-search-button');
        const feedback = document.getElementById('campaign-map-feedback');
        const coordinates = document.getElementById('campaign-map-coordinates');

        const defaultCenter = [3.0738, 101.5183]; // Selangor
        const hasSavedPin = latitudeInput.value !== '' && longitudeInput.value !== '';
        const initialCenter = hasSavedPin
            ? [parseFloat(latitudeInput.value), parseFloat(longitudeInput.value)]
            : defaultCenter;

        const map = L.map(mapElement).setView(initialCenter, hasSavedPin ? 16 : 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = null;

        const updateCoordinates = (latlng) => {
            latitudeInput.value = latlng.lat.toFixed(7);
            longitudeInput.value = latlng.lng.toFixed(7);
            coordinates.textContent = `${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
            feedback.textContent = 'Pin selected. Drag it or click elsewhere to adjust the location.';
        };

        const placeMarker = (latlng, zoom = 16) => {
            if (!marker) {
                marker = L.marker(latlng, { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const position = marker.getLatLng();
                    updateCoordinates(position);
                    reverseGeocode(position);
                });
            } else {
                marker.setLatLng(latlng);
            }
            map.setView(latlng, zoom);
            updateCoordinates(latlng);
        };

        const reverseGeocode = async (latlng) => {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`;
                const response = await fetch(url, { headers: { 'Accept-Language': 'en' } });
                if (!response.ok) return;
                const result = await response.json();
                if (result.display_name && locationInput) locationInput.value = result.display_name;
            } catch (error) {
                feedback.textContent = 'Pin saved. Enter the venue/address manually if the address lookup is unavailable.';
            }
        };

        map.on('click', (event) => {
            placeMarker(event.latlng);
            reverseGeocode(event.latlng);
        });

        if (hasSavedPin) {
            placeMarker({ lat: initialCenter[0], lng: initialCenter[1] }, 16);
        }

        const searchLocation = async () => {
            const query = (searchInput.value || '').trim();
            if (!query) {
                feedback.textContent = 'Enter a venue or address to search.';
                return;
            }
            searchButton.disabled = true;
            searchButton.textContent = 'Searching...';
            feedback.textContent = 'Searching OpenStreetMap...';
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=my&q=${encodeURIComponent(query)}`;
                const response = await fetch(url, { headers: { 'Accept-Language': 'en' } });
                if (!response.ok) throw new Error('Search unavailable');
                const results = await response.json();
                if (!results.length) {
                    feedback.textContent = 'No matching place found. Try a more specific address or pin the map manually.';
                    return;
                }
                const result = results[0];
                const latlng = { lat: parseFloat(result.lat), lng: parseFloat(result.lon) };
                placeMarker(latlng, 16);
                if (locationInput) locationInput.value = result.display_name;
                feedback.textContent = 'Location found. Adjust the pin to the exact entrance or meeting point.';
            } catch (error) {
                feedback.textContent = 'Location search is temporarily unavailable. You can still click the map to place a pin.';
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'Find on map';
            }
        };

        searchButton.addEventListener('click', searchLocation);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchLocation();
            }
        });

        setTimeout(() => map.invalidateSize(), 150);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCampaignLocationMap);
    } else {
        initCampaignLocationMap();
    }
})();
</script>
