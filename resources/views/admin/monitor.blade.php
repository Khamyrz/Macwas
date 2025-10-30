@extends('layouts.app')

@section('title', 'Login Monitoring')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            <h1 class="text-2xl font-semibold">Login Monitoring</h1>
        </div>

        <div class="col-span-12 grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-3 border rounded p-4">
                <div class="text-sm text-gray-500">Total Attempts</div>
                <div id="stat-total" class="text-2xl font-bold">0</div>
            </div>
            <div class="col-span-12 md:col-span-3 border rounded p-4">
                <div class="text-sm text-gray-500">Success</div>
                <div id="stat-success" class="text-2xl font-bold text-green-600">0</div>
            </div>
            <div class="col-span-12 md:col-span-3 border rounded p-4">
                <div class="text-sm text-gray-500">Failed</div>
                <div id="stat-failed" class="text-2xl font-bold text-red-600">0</div>
            </div>
            <div class="col-span-12 md:col-span-3 border rounded p-4">
                <div class="text-sm text-gray-500">Unique IPs</div>
                <div id="stat-unique-ips" class="text-2xl font-bold">0</div>
            </div>
        </div>

        <div class="col-span-12 border rounded p-4">
            <form id="filters" class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Date From</label>
                    <input type="date" id="date_from" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Date To</label>
                    <input type="date" id="date_to" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select id="status" class="w-full border rounded px-3 py-2">
                        <option value="">Any</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="text" id="email" placeholder="Filter by email" class="w-full border rounded px-3 py-2" />
                </div>
                <div class="col-span-12 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
                    <button type="button" id="resetFilters" class="px-4 py-2 bg-gray-200 rounded">Reset</button>
                </div>
            </form>
        </div>

        <div class="col-span-12 border rounded">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 border">Email</th>
                            <th class="px-3 py-2 border">IP Address</th>
                            <th class="px-3 py-2 border">Visitor Address</th>
                            <th class="px-3 py-2 border">Attempts (5m)</th>
                            <th class="px-3 py-2 border">Status</th>
                            <th class="px-3 py-2 border">Attempted At</th>
                            <th class="px-3 py-2 border">User Agent</th>
                            <th class="px-3 py-2 border">Map</th>
                        </tr>
                    </thead>
                    <tbody id="attempts-body">
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="pagination" class="flex items-center justify-between p-3 border-t">
                <button id="prevPage" class="px-3 py-1 border rounded disabled:opacity-50" disabled>Prev</button>
                <div id="pageInfo" class="text-sm text-gray-600">Page 1</div>
                <button id="nextPage" class="px-3 py-1 border rounded disabled:opacity-50" disabled>Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-full max-w-3xl">
        <div class="flex items-center justify-between border-b px-4 py-2">
            <h3 class="font-semibold">Visitor Location</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-800">✕</button>
        </div>
        <div class="p-0">
            <div id="map" style="height: 420px;"></div>
        </div>
        <div class="flex justify-end gap-2 px-4 py-2 border-t">
            <button id="closeModalBottom" class="px-4 py-2 bg-gray-200 rounded">Close</button>
        </div>
    </div>
    <style>
        .leaflet-control-attribution,
        .leaflet-control-logo,
        .leaflet-control-container .copyright {
            display: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('attempts-body');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const filtersForm = document.getElementById('filters');
    const resetFiltersBtn = document.getElementById('resetFilters');

    let currentPage = 1;
    let lastPage = 1;

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"]+/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
    }

    function getFilters() {
        return {
            date_from: document.getElementById('date_from').value || '',
            date_to: document.getElementById('date_to').value || '',
            status: document.getElementById('status').value || '',
            email: document.getElementById('email').value || ''
        };
    }

    function buildQuery(params) {
        const esc = encodeURIComponent;
        const query = Object.keys(params)
            .filter(k => params[k] !== '')
            .map(k => `${esc(k)}=${esc(params[k])}`)
            .join('&');
        return query ? '?' + query : '';
    }

    async function fetchData(page = 1) {
        const params = getFilters();
        const query = buildQuery(params);
        const url = `{{ route('admin.monitoring.data') }}?page=${page}${query ? '&' + query.slice(1) : ''}`;

        tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">Loading...</td></tr>';

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
            const data = await res.json();

            currentPage = data.pagination.current_page;
            lastPage = data.pagination.last_page;
            pageInfo.textContent = `Page ${currentPage} of ${lastPage} | Total ${data.pagination.total}`;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= lastPage;

            document.getElementById('stat-total').textContent = data.stats.total_attempts;
            document.getElementById('stat-success').textContent = data.stats.successful_attempts;
            document.getElementById('stat-failed').textContent = data.stats.failed_attempts;
            document.getElementById('stat-unique-ips').textContent = data.stats.unique_ips;

            if (!data.attempts || data.attempts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">No data</td></tr>';
                return;
            }

            const rows = data.attempts.map(a => {
                const address = [a.location.city, a.location.region, a.location.country].filter(Boolean).join(', ');
                const attemptsCount = (typeof a.attempts_count === 'number') ? a.attempts_count : 0;
                const badgeClass = a.success ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100';
                return `<tr>
                    <td class="px-3 py-2 border align-top">${escapeHtml(a.email)}</td>
                    <td class="px-3 py-2 border align-top">${escapeHtml(a.ip_address)}</td>
                    <td class="px-3 py-2 border align-top">${escapeHtml(address)}</td>
                    <td class="px-3 py-2 border align-top text-center">${attemptsCount}</td>
                    <td class="px-2 py-2 border align-top"><span class="px-2 py-1 rounded text-xs ${badgeClass}">${a.status}</span></td>
                    <td class="px-3 py-2 border align-top">${escapeHtml(a.attempted_at)}</td>
                    <td class="px-3 py-2 border align-top truncate max-w-xs" title="${escapeHtml(a.user_agent)}">${escapeHtml(a.user_agent || '')}</td>
                    <td class="px-3 py-2 border align-top text-center">
                        <button data-lat="${a.coordinates.lat}" data-lng="${a.coordinates.lng}" data-addr="${escapeHtml(address)}" class="open-map px-3 py-1 border rounded">View</button>
                    </td>
                </tr>`;
            }).join('');

            tbody.innerHTML = rows;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-red-600">Failed to load data</td></tr>';
        }
    }

    filtersForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchData(1);
    });

    resetFiltersBtn.addEventListener('click', function() {
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        document.getElementById('status').value = '';
        document.getElementById('email').value = '';
        fetchData(1);
    });

    prevBtn.addEventListener('click', function() {
        if (currentPage > 1) fetchData(currentPage - 1);
    });
    nextBtn.addEventListener('click', function() {
        if (currentPage < lastPage) fetchData(currentPage + 1);
    });

    // Modal + Leaflet map
    const modal = document.getElementById('mapModal');
    const closeModal = () => { modal.classList.add('hidden'); };
    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.getElementById('closeModalBottom').addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });

    let map, marker;
    function openMap(lat, lng, addr) {
        modal.classList.remove('hidden');
        setTimeout(function() {
            if (!map) {
                map = L.map('map', { zoomControl: true, attributionControl: false });
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
            }
            map.setView([lat || 0, lng || 0], (lat && lng) ? 12 : 2);
            if (marker) { marker.remove(); }
            marker = L.marker([lat || 0, lng || 0]).addTo(map);
            marker.bindPopup(addr || 'Unknown').openPopup();
            setTimeout(() => map.invalidateSize(), 50);
        }, 10);
    }

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-map');
        if (!btn) return;
        const lat = parseFloat(btn.getAttribute('data-lat')) || 0;
        const lng = parseFloat(btn.getAttribute('data-lng')) || 0;
        const addr = btn.getAttribute('data-addr') || '';
        openMap(lat, lng, addr);
    });

    fetchData(1);
});
</script>
@endpush


