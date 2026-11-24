@php($campaignListUrl = $campaignListUrl ?? null)
@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endonce

<section class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
    <div class="flex flex-col justify-between gap-3 border-b border-slate-100 bg-white p-5 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">{{ $mapTitle }}</h2>
            <p class="mt-1 max-w-3xl text-sm text-slate-500">Explore all nine Selangor districts. Select a campaign pin to open that campaign in the list below.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700">9 Selangor districts</span>
            <span id="{{ $mapId }}-event-count" class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">{{ count($campaignMapPoints) }} campaign pins</span>
        </div>
    </div>

    <div class="relative bg-slate-100">
        <div id="{{ $mapId }}" class="z-0 h-[420px] w-full sm:h-[480px] lg:h-[540px]"
            aria-label="Interactive Selangor district map with clickable campaign event pins"></div>
        <div id="{{ $mapId }}-loading"
            class="absolute inset-x-4 top-4 z-[500] rounded-xl border border-white/15 bg-slate-900/90 px-4 py-3 text-sm font-medium text-white shadow-lg">
            Loading Selangor district boundaries...
        </div>
    </div>

    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-500">District zoning and campaign count</h3>
            <span class="text-xs text-slate-500">Select a district to focus the map</span>
        </div>
        <div id="{{ $mapId }}-district-legend" class="flex flex-wrap gap-2" aria-label="Selangor district legend">
            <span class="text-xs text-slate-500">District legend will appear after the map loads.</span>
        </div>
    </div>

    @if(empty($campaignListUrl))
    <div class="p-5">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Campaign details</h3>
                <p class="mt-1 text-xs text-slate-500">Select a campaign pin to view its details here.</p>
            </div>
            <span id="{{ $mapId }}-selection-label" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">No event selected</span>
        </div>

        <div id="{{ $mapId }}-event-empty" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-center">
            <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">Select a campaign pin</p>
            <p class="mt-1 text-xs text-slate-500">The selected campaign's name, district, status, dates and description will appear here.</p>
        </div>

        <article id="{{ $mapId }}-event-selected" class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-live="polite">
            <div class="grid gap-0 md:grid-cols-[minmax(0,1fr)_220px]">
                <div class="min-w-0 p-5">
                    <p id="{{ $mapId }}-event-district" class="mb-2 text-xs font-extrabold uppercase tracking-[0.14em] text-sky-700"></p>
                    <h4 id="{{ $mapId }}-event-name" class="break-words text-xl font-extrabold text-slate-900"></h4>
                    <p id="{{ $mapId }}-event-description" class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-slate-600"></p>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 p-5 md:border-l md:border-t-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Campaign status</p>
                    <p id="{{ $mapId }}-event-status" class="mt-1 break-words text-sm font-bold text-rose-700"></p>
                    <p class="mt-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Programme dates</p>
                    <p id="{{ $mapId }}-event-dates" class="mt-1 break-words text-sm font-semibold leading-5 text-slate-700"></p>
                </div>
            </div>
        </article>

        <p id="{{ $mapId }}-map-message" class="mt-3 text-xs leading-5 text-slate-500" aria-live="polite"></p>
    </div>

    @else
        <div class="px-5 py-3">
            <p id="{{ $mapId }}-map-message" class="text-xs leading-5 text-slate-500" aria-live="polite">Select a district to filter the Existing Campaigns list, or select a campaign pin to open its details in that list.</p>
        </div>
    @endif

    <div class="border-t border-slate-100 bg-slate-50 px-5 py-3 text-xs leading-5 text-slate-500">
        District boundaries are from the Selangor State GIS Portal. Campaign pins are approximate district locations, not exact venue addresses.
    </div>
</section>

<style>
    /* Clean dashboard styling: neutral canvas, muted district fills, crisp boundaries. */
    #{{ $mapId }} { background: #f1f5f9; }
    #{{ $mapId }}.leaflet-container {
        background: #f1f5f9;
        color: #334155;
        font-family: inherit;
    }
    #{{ $mapId }} .leaflet-control-attribution {
        border-radius: 6px 0 0 0;
        background: rgba(255,255,255,.88);
        color: #64748b;
        font-size: 10px;
    }
    #{{ $mapId }} .leaflet-control-attribution a { color: #2563eb; }
    #{{ $mapId }} .leaflet-bar a {
        border-color: #dbe3ec;
        background: #fff;
        color: #334155;
    }
    #{{ $mapId }} .leaflet-bar a:hover { background: #f8fafc; }

    #{{ $mapId }} .scc-district-label-marker { background: transparent; border: 0; }
    #{{ $mapId }} .scc-district-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translate(-50%, -50%);
        white-space: nowrap;
        border: 1px solid rgba(100,116,139,.28);
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        color: #334155;
        padding: 5px 9px;
        font-size: 11px;
        line-height: 1.15;
        font-weight: 750;
        letter-spacing: 0;
        box-shadow: 0 1px 5px rgba(15,23,42,.12);
        pointer-events: none;
    }

    #{{ $mapId }} .scc-event-pin-wrap { background: transparent; border: 0; }
    #{{ $mapId }} .scc-event-pin {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #fff;
        border-radius: 50% 50% 50% 7px;
        transform: rotate(-45deg);
        background: #2563eb;
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        box-shadow: 0 2px 8px rgba(15,23,42,.22);
        transition: transform .16s ease, background .16s ease, box-shadow .16s ease;
    }
    #{{ $mapId }} .scc-event-pin span {
        width: 8px; height: 8px; border-radius: 999px;
        background: #fff; transform: rotate(45deg);
    }
    #{{ $mapId }} .scc-event-pin.is-hovered {
        background: #1d4ed8;
        transform: rotate(-45deg) scale(1.10);
        box-shadow: 0 4px 12px rgba(37,99,235,.28);
    }
    #{{ $mapId }} .scc-event-pin.is-selected {
        background: #0f766e;
        color: #fff;
        transform: rotate(-45deg) scale(1.13);
        box-shadow: 0 4px 13px rgba(15,118,110,.30);
    }

    #{{ $mapId }} .scc-event-popup .leaflet-popup-content-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 8px 22px rgba(15,23,42,.16);
    }
    #{{ $mapId }} .scc-event-popup .leaflet-popup-content {
        width: 240px !important;
        max-width: calc(100vw - 72px);
        margin: 12px 14px;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    #{{ $mapId }} .scc-event-popup .leaflet-popup-tip { background: #fff; }
    #{{ $mapId }} .scc-event-popup-title {
        overflow-wrap: anywhere;
        word-break: break-word;
        font-size: 14px;
        line-height: 1.4;
        font-weight: 800;
        color: #0f172a;
    }
    #{{ $mapId }} .scc-event-popup-description {
        max-height: 90px;
        overflow: auto;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        word-break: break-word;
        font-size: 11px;
        line-height: 1.5;
        color: #64748b;
    }

    #{{ $mapId }}-district-legend .scc-legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #fff;
        padding: 6px 10px;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    #{{ $mapId }}-district-legend .scc-legend-chip:hover,
    #{{ $mapId }}-district-legend .scc-legend-chip:focus-visible {
        border-color: #93c5fd;
        background: #f8fbff;
        box-shadow: 0 0 0 3px rgba(59,130,246,.10);
        outline: none;
    }
    #{{ $mapId }}-district-legend .scc-legend-swatch {
        width: 11px;
        height: 11px;
        border: 1px solid rgba(51,65,85,.16);
        border-radius: 3px;
    }
    #{{ $mapId }}-district-legend .scc-legend-count {
        min-width: 18px;
        border-radius: 999px;
        background: #f1f5f9;
        padding: 1px 5px;
        text-align: center;
        color: #475569;
        font-size: 10px;
    }
</style>

<script>
(function () {
    'use strict';

    const mapId = @json($mapId);
    const points = @json($campaignMapPoints);
    const campaignListUrl = @json($campaignListUrl);
    const selectedCampaignId = @json(isset($selectedMapCampaignId) ? (string) $selectedMapCampaignId : null);
    const selectedDistrict = @json($selectedDistrict ?? null);
    const boundaryUrl = 'https://jpsselgis.selangor.gov.my/gis/rest/services/Sempadan_MIL1/MapServer/1/query?where=1%3D1&outFields=Negeri%2CDaerah%2CNam&returnGeometry=true&outSR=4326&f=geojson';

    const districtNames = [
        'Sabak Bernam', 'Kuala Selangor', 'Hulu Selangor', 'Gombak', 'Petaling',
        'Klang', 'Kuala Langat', 'Sepang', 'Hulu Langat'
    ];
    const districtPalette = {
        'sabak bernam': '#dbeafe',
        'kuala selangor': '#d8eeef',
        'hulu selangor': '#e1eedc',
        'gombak': '#e8e2f2',
        'petaling': '#f2eadb',
        'klang': '#dcece3',
        'kuala langat': '#f1edda',
        'sepang': '#e1e8f3',
        'hulu langat': '#f1e3df'
    };
    const districtAliases = {
        'ulu selangor': 'hulu selangor',
        'ulu langat': 'hulu langat'
    };

    const normalizeDistrict = (value) => {
        let key = String(value || '').trim().toLowerCase()
            .replace(/[^a-z0-9]+/g, ' ').replace(/\s+/g, ' ').trim();
        return districtAliases[key] || key;
    };

    const openCampaignListForDistrict = (districtName) => {
        if (!campaignListUrl) return false;
        const target = new URL(campaignListUrl, window.location.origin);
        target.searchParams.set('district', districtName);
        target.searchParams.delete('map_campaign_id');
        target.hash = 'existing-campaigns';
        window.location.assign(target.toString());
        return true;
    };

    const expandExistingCampaignRow = (event) => {
        if (!campaignListUrl || event.id == null) return false;
        const row = document.getElementById('campaign-row-' + String(event.id));
        const details = document.getElementById('campaign-details-row-' + String(event.id));
        if (!row || !details) return false;

        document.querySelectorAll('.campaign-details-row').forEach((item) => item.classList.add('hidden'));
        document.querySelectorAll('.campaign-row').forEach((item) => {
            item.classList.remove('bg-teal-50', 'ring-2', 'ring-inset', 'ring-teal-500');
        });

        details.classList.remove('hidden');
        row.classList.add('bg-teal-50', 'ring-2', 'ring-inset', 'ring-teal-500');
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true;
    };

    const openCampaignListForEvent = (event) => {
        if (!campaignListUrl || event.id == null) return false;
        const target = new URL(campaignListUrl, window.location.origin);
        target.searchParams.set('map_campaign_id', String(event.id));
        target.searchParams.delete('district');
        target.hash = 'campaign-row-' + String(event.id);
        window.location.assign(target.toString());
        return true;
    };

    const makeText = (tag, className, value) => {
        const element = document.createElement(tag);
        if (className) element.className = className;
        element.textContent = value == null ? '' : String(value);
        return element;
    };

    const formatDate = (value) => {
        if (!value) return '';
        const parsed = new Date(String(value).slice(0, 10) + 'T00:00:00');
        return Number.isNaN(parsed.getTime()) ? String(value) : parsed.toLocaleDateString('en-MY', {
            day: 'numeric', month: 'short', year: 'numeric'
        });
    };

    const initMap = async () => {
        const element = document.getElementById(mapId);
        const loading = document.getElementById(mapId + '-loading');
        const legend = document.getElementById(mapId + '-district-legend');
        const message = document.getElementById(mapId + '-map-message');
        if (!element || element.dataset.initialized === '1') return;
        if (typeof L === 'undefined') {
            if (loading) loading.textContent = 'The map library could not be loaded. Check your internet connection and refresh the page.';
            return;
        }
        element.dataset.initialized = '1';

        const map = L.map(element, {
            scrollWheelZoom: false,
            zoomControl: true,
            preferCanvas: false,
            zoomSnap: 0.25
        });
        map.attributionControl.setPrefix('');
        L.control.scale({ imperial: false, maxWidth: 110 }).addTo(map);

        const districtLayers = new Map();
        const districtCounts = new Map(districtNames.map(name => [normalizeDistrict(name), 0]));
        const eventMarkers = [];
        let selectedMarker = null;

        const setSelectedEvent = (event, districtName, marker) => {
            const empty = document.getElementById(mapId + '-event-empty');
            const card = document.getElementById(mapId + '-event-selected');
            const selectionLabel = document.getElementById(mapId + '-selection-label');
            const district = document.getElementById(mapId + '-event-district');
            const name = document.getElementById(mapId + '-event-name');
            const status = document.getElementById(mapId + '-event-status');
            const dates = document.getElementById(mapId + '-event-dates');
            const description = document.getElementById(mapId + '-event-description');

            if (empty) empty.classList.add('hidden');
            if (card) card.classList.remove('hidden');
            if (selectionLabel) selectionLabel.textContent = 'Campaign selected';
            if (district) district.textContent = districtName + ' District';
            if (name) name.textContent = event.name || 'Community Campaign';
            if (status) status.textContent = event.status || 'Status not provided';
            if (dates) {
                const from = formatDate(event.start_date);
                const to = formatDate(event.end_date);
                dates.textContent = from && to ? from + ' – ' + to : (from || to || 'Dates not provided');
            }
            if (description) description.textContent = event.description || 'No campaign description has been provided.';
            if (message) message.textContent = 'Showing details for ' + (event.name || 'Community Campaign') + ' in ' + districtName + '.';

            if (selectedMarker && selectedMarker !== marker) {
                selectedMarker.setIcon(createEventIcon(false));
            }
            selectedMarker = marker;
            marker.setIcon(createEventIcon(true));
            marker.setZIndexOffset(1000);
        };

        const createEventIcon = (selected, hovered = false) => L.divIcon({
            className: 'scc-event-pin-wrap',
            html: '<div class="scc-event-pin' + (selected ? ' is-selected' : (hovered ? ' is-hovered' : '')) + '"><span aria-hidden="true"></span></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 32]
        });

        const createEventPopup = (event, districtName) => {
            const wrapper = document.createElement('div');
            wrapper.appendChild(makeText('div', 'mb-1 text-[10px] font-extrabold uppercase tracking-[.12em] text-sky-700', districtName + ' District'));
            wrapper.appendChild(makeText('div', 'scc-event-popup-title', event.name || 'Community Campaign'));
            wrapper.appendChild(makeText('div', 'mt-2 text-xs font-extrabold text-rose-700', event.status || 'Status not provided'));
            const from = formatDate(event.start_date);
            const to = formatDate(event.end_date);
            const dateText = from && to ? from + ' – ' + to : (from || to || 'Dates not provided');
            wrapper.appendChild(makeText('div', 'mt-1 text-[11px] font-semibold text-slate-600', dateText));
            wrapper.appendChild(makeText('div', 'scc-event-popup-description mt-2', event.description || 'No campaign description has been provided.'));
            return wrapper;
        };

        try {
            const response = await fetch(boundaryUrl, {
                method: 'GET',
                headers: { 'Accept': 'application/geo+json, application/json' },
                mode: 'cors'
            });
            if (!response.ok) throw new Error('GIS service returned HTTP ' + response.status);
            const geojson = await response.json();
            if (!geojson || !Array.isArray(geojson.features) || geojson.features.length === 0) {
                throw new Error('The GIS service returned no district polygons.');
            }

            const featureByDistrict = new Map();
            geojson.features.forEach((feature) => {
                const props = feature.properties || {};
                const state = String(props.Negeri || props.NEGERI || props.state || props.STATE || 'Selangor').toLowerCase();
                if (!state.includes('selangor')) return;
                const districtRaw = props.Daerah || props.DAERAH || props.district || props.DISTRICT || props.Nam || props.NAM || '';
                const key = normalizeDistrict(districtRaw);
                if (!districtNames.some(name => normalizeDistrict(name) === key)) return;
                if (!featureByDistrict.has(key)) featureByDistrict.set(key, []);
                featureByDistrict.get(key).push(feature);
            });

            const missing = districtNames.filter(name => !featureByDistrict.has(normalizeDistrict(name)));
            if (missing.length) throw new Error('District layer is missing: ' + missing.join(', '));

            const districtGroup = L.featureGroup().addTo(map);
            districtNames.forEach((districtName) => {
                const key = normalizeDistrict(districtName);
                const features = featureByDistrict.get(key);
                const baseStyle = {
                    color: '#64748b',
                    weight: 1.7,
                    opacity: 0.92,
                    fillColor: districtPalette[key],
                    fillOpacity: 0.94,
                    lineJoin: 'round'
                };

                const zone = L.geoJSON({ type: 'FeatureCollection', features }, {
                    style: () => ({ ...baseStyle }),
                    onEachFeature: (feature, layer) => {
                        layer.on({
                            mouseover: () => {
                                layer.setStyle({ color: '#0f766e', weight: 3.8, opacity: 1, fillOpacity: 1 });
                                if (layer.bringToFront) layer.bringToFront();
                            },
                            mouseout: () => layer.setStyle({ ...baseStyle }),
                            click: () => {
                                if (openCampaignListForDistrict(districtName)) return;
                                const bounds = zone.getBounds();
                                map.fitBounds(bounds, { padding: [55, 55], maxZoom: 10 });
                                if (message) {
                                    const count = districtCounts.get(key) || 0;
                                    message.textContent = districtName + ' District: ' + count + (count === 1 ? ' campaign pin.' : ' campaign pins.') + ' Click a pin to open its details.';
                                }
                            }
                        });
                    }
                }).addTo(districtGroup);

                districtLayers.set(key, zone);
                const center = zone.getBounds().getCenter();
                const label = L.divIcon({
                    className: 'scc-district-label-marker',
                    html: '<div class="scc-district-label">' + districtName + '</div>',
                    iconSize: [1, 1],
                    iconAnchor: [0, 0]
                });
                L.marker(center, { icon: label, interactive: false, keyboard: false, zIndexOffset: 300 }).addTo(map);
            });

            const overallBounds = districtGroup.getBounds();
            map.fitBounds(overallBounds, { padding: [28, 28] });
            map.setMaxBounds(overallBounds.pad(0.24));
            map.setMinZoom(Math.max(6, map.getZoom() - 1.25));

            const eventsByDistrict = new Map();
            const unmappedEvents = [];
            points.forEach((event) => {
                const key = normalizeDistrict(event.district || event.location);
                if (!districtLayers.has(key)) {
                    unmappedEvents.push(event);
                    return;
                }
                if (!eventsByDistrict.has(key)) eventsByDistrict.set(key, []);
                eventsByDistrict.get(key).push(event);
            });

            eventsByDistrict.forEach((events, key) => {
                const zone = districtLayers.get(key);
                const bounds = zone.getBounds();
                const center = bounds.getCenter();
                const latSpan = Math.max(bounds.getNorth() - bounds.getSouth(), 0.008);
                const lngSpan = Math.max(bounds.getEast() - bounds.getWest(), 0.008);
                const radiusY = Math.min(latSpan * 0.11, 0.045);
                const radiusX = Math.min(lngSpan * 0.11, 0.045);

                events.forEach((event, index) => {
                    // One marker per campaign. Markers in the same district are offset slightly
                    // around the district centre because campaigns store district, not venue GPS.
                    const ring = Math.floor(index / 8);
                    const slot = index % 8;
                    const angle = (slot / Math.min(events.length, 8)) * Math.PI * 2 - Math.PI / 2;
                    const spread = 1 + (ring * 0.85);
                    const position = L.latLng(
                        center.lat + Math.sin(angle) * radiusY * spread,
                        center.lng + Math.cos(angle) * radiusX * spread
                    );
                    const marker = L.marker(position, {
                        icon: createEventIcon(false),
                        keyboard: true,
                        riseOnHover: true,
                        title: (event.name || 'Community Campaign') + ' — ' + districtNames.find(n => normalizeDistrict(n) === key),
                        alt: 'Campaign location in ' + districtNames.find(n => normalizeDistrict(n) === key)
                    }).addTo(map);
                    if (selectedCampaignId && String(event.id) === String(selectedCampaignId)) {
                        marker.setIcon(createEventIcon(true));
                        selectedMarker = marker;
                    }
                    const currentDistrictName = districtNames.find(n => normalizeDistrict(n) === key);
                    marker.bindPopup(createEventPopup(event, currentDistrictName), {
                        maxWidth: 300, closeButton: true, autoPan: true,
                        className: 'scc-event-popup', keepInView: true
                    });
                    marker.on('mouseover', () => {
                        if (selectedMarker !== marker) {
                            marker.setIcon(createEventIcon(false, true));
                        }
                    });
                    marker.on('mouseout', () => {
                        if (selectedMarker !== marker) {
                            marker.setIcon(createEventIcon(false, false));
                        }
                    });
                    marker.on('click', () => {
                        if (campaignListUrl) {
                            if (!expandExistingCampaignRow(event)) {
                                openCampaignListForEvent(event);
                                return;
                            }
                            if (selectedMarker && selectedMarker !== marker) {
                                selectedMarker.setIcon(createEventIcon(false));
                            }
                            selectedMarker = marker;
                            marker.setIcon(createEventIcon(true));
                            marker.setZIndexOffset(1000);
                            if (message) {
                                message.textContent = 'Selected ' + (event.name || 'Community Campaign') + ' in ' + currentDistrictName + '. Its details are expanded in Existing Campaigns below.';
                            }
                            return;
                        }
                        setSelectedEvent(event, currentDistrictName, marker);
                        marker.openPopup();
                    });
                    eventMarkers.push(marker);
                });
            });

            districtNames.forEach((districtName) => {
                const key = normalizeDistrict(districtName);
                districtCounts.set(key, (eventsByDistrict.get(key) || []).length);
            });

            if (legend) {
                legend.replaceChildren();
                districtNames.forEach((districtName) => {
                    const key = normalizeDistrict(districtName);
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'scc-legend-chip';
                    chip.title = 'Focus map on ' + districtName;
                    const swatch = makeText('span', 'scc-legend-swatch', '');
                    swatch.style.backgroundColor = districtPalette[key];
                    const name = makeText('span', '', districtName);
                    const count = makeText('span', 'scc-legend-count', districtCounts.get(key) || 0);
                    chip.append(swatch, name, count);
                    if (selectedDistrict && normalizeDistrict(selectedDistrict) === key) {
                        chip.classList.add('ring-2', 'ring-teal-500', 'bg-teal-50');
                    }
                    chip.addEventListener('click', () => {
                        if (openCampaignListForDistrict(districtName)) return;
                        const zone = districtLayers.get(key);
                        if (zone) map.fitBounds(zone.getBounds(), { padding: [55, 55], maxZoom: 10 });
                        if (message) {
                            const total = districtCounts.get(key) || 0;
                            message.textContent = districtName + ' District: ' + total + (total === 1 ? ' campaign pin.' : ' campaign pins.') + ' Click a pin to open its details.';
                        }
                    });
                    legend.appendChild(chip);
                });
            }

            if (selectedMarker) {
                map.panTo(selectedMarker.getLatLng());
            } else if (selectedDistrict) {
                const selectedZone = districtLayers.get(normalizeDistrict(selectedDistrict));
                if (selectedZone) map.fitBounds(selectedZone.getBounds(), { padding: [55, 55], maxZoom: 10 });
            }

            const mappedCount = eventMarkers.length;
            const counter = document.getElementById(mapId + '-event-count');
            if (counter) counter.textContent = mappedCount + ' campaign pins';
            if (loading) {
                loading.textContent = unmappedEvents.length
                    ? mappedCount + ' campaign pin(s) mapped. ' + unmappedEvents.length + ' campaign(s) have no recognised district and are not pinned.'
                    : (mappedCount ? 'All ' + mappedCount + ' campaign pin(s) are ready. Click a pin to view its details.' : 'All nine district zones are shown. No campaigns are available to display.');
                if (unmappedEvents.length === 0) window.setTimeout(() => loading.remove(), 6000);
            }
            if (message && unmappedEvents.length) {
                message.textContent = unmappedEvents.length + ' campaign(s) are not pinned because their location does not match one of the nine district names. Update those campaign locations to a district.';
            }

            window.setTimeout(() => map.invalidateSize(), 180);
        } catch (error) {
            console.error('Selangor campaign map failed:', error);
            if (loading) {
                loading.textContent = 'The district map could not be loaded from the Selangor GIS Portal. Please refresh the page or check the internet connection. (' + error.message + ')';
                loading.classList.add('text-rose-200');
            }
        }
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initMap);
    else initMap();
})();
</script>
