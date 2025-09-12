<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Peserta Event Lari</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin - Event Lari Bayan Run 2025</h1>
                <p class="text-gray-600">Kelola pendaftaran peserta event lari</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500 rounded-lg">
                            <span class="text-white text-2xl">👥</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Peserta</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pesertaLaris->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500 rounded-lg">
                            <span class="text-white text-2xl">✅</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Terdaftar Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $pesertaLaris->where('created_at', '>=', today())->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-500 rounded-lg">
                            <span class="text-white text-2xl">🏃‍♂️</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Kategori Terpopuler</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $pesertaLaris->groupBy('kategori_lari')->sortByDesc(function($group) { return $group->count(); })->keys()->first() ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500 rounded-lg">
                            <span class="text-white text-2xl">📱</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">QR Code</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pesertaLaris->whereNotNull('qr_code_path')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-4 mb-6">
                <button onclick="exportData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    📊 Export Data
                </button>
                <button onclick="refreshData()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    🔄 Refresh
                </button>
                <a href="/daftar" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    ➕ Form Pendaftaran
                </a>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Filter Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Lari</label>
                        <select id="filter-kategori" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Semua Kategori</option>
                            @foreach($pesertaLaris->pluck('kategori_lari')->unique()->sort() as $kategori)
                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="filter-status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Semua Status</option>
                            <option value="terdaftar">Terdaftar</option>
                            <option value="konfirmasi">Konfirmasi</option>
                            <option value="hadir">Hadir</option>
                            <option value="tidak_hadir">Tidak Hadir</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                        <input type="text" id="search" placeholder="Cari nama atau email..." class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Daftar Peserta Event Lari</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peserta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor BIB
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Daftar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="peserta-table-body" class="bg-white divide-y divide-gray-200">
                            @forelse($pesertaLaris as $index => $peserta)
                                <tr class="peserta-row" 
                                    data-kategori="{{ $peserta->kategori_lari }}" 
                                    data-status="{{ $peserta->status }}"
                                    data-search="{{ strtolower($peserta->nama_lengkap . ' ' . $peserta->email) }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $peserta->nama_lengkap }}</div>
                                                <div class="text-sm text-gray-500">{{ $peserta->email }}</div>
                                                <div class="text-sm text-gray-500">{{ $peserta->telepon }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $peserta->kategori_lari }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $peserta->nomor_bib }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($peserta->status === 'terdaftar') bg-yellow-100 text-yellow-800
                                            @elseif($peserta->status === 'konfirmasi') bg-blue-100 text-blue-800
                                            @elseif($peserta->status === 'hadir') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($peserta->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $peserta->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewDetail({{ $peserta->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                👁️ Lihat
                                            </button>
                                            @if($peserta->qr_code_path)
                                                <button onclick="showQR('{{ $peserta->qr_code_url }}')" 
                                                        class="text-green-600 hover:text-green-900">
                                                    📱 QR
                                                </button>
                                            @endif
                                            <button onclick="updateStatus({{ $peserta->id }})" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                ✏️ Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada peserta yang terdaftar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div id="qr-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">QR Code Peserta</h3>
                <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Close</span>
                    ✕
                </button>
            </div>
            <div class="text-center">
                <img id="qr-image" src="" alt="QR Code" class="mx-auto w-64 h-64 border rounded-lg">
            </div>
        </div>
    </div>

    <script>
        // Filter functions
        document.getElementById('filter-kategori').addEventListener('change', filterTable);
        document.getElementById('filter-status').addEventListener('change', filterTable);
        document.getElementById('search').addEventListener('input', filterTable);

        function filterTable() {
            const kategoriFilter = document.getElementById('filter-kategori').value.toLowerCase();
            const statusFilter = document.getElementById('filter-status').value.toLowerCase();
            const searchFilter = document.getElementById('search').value.toLowerCase();
            
            const rows = document.querySelectorAll('.peserta-row');
            
            rows.forEach(row => {
                const kategori = row.dataset.kategori.toLowerCase();
                const status = row.dataset.status.toLowerCase();
                const searchText = row.dataset.search;
                
                const kategoriMatch = !kategoriFilter || kategori.includes(kategoriFilter);
                const statusMatch = !statusFilter || status === statusFilter;
                const searchMatch = !searchFilter || searchText.includes(searchFilter);
                
                if (kategoriMatch && statusMatch && searchMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Actions
        function viewDetail(id) {
            window.location.href = `/admin/peserta/${id}`;
        }

        function showQR(url) {
            document.getElementById('qr-image').src = url;
            document.getElementById('qr-modal').classList.remove('hidden');
            document.getElementById('qr-modal').classList.add('flex');
        }

        function closeQRModal() {
            document.getElementById('qr-modal').classList.add('hidden');
            document.getElementById('qr-modal').classList.remove('flex');
        }

        function updateStatus(id) {
            const newStatus = prompt('Masukkan status baru (terdaftar, konfirmasi, hadir, tidak_hadir):');
            if (newStatus) {
                // Implement status update logic
                alert('Fitur update status akan diimplementasikan');
            }
        }

        function exportData() {
            window.location.href = '/admin/export?format=excel';
        }

        function refreshData() {
            window.location.reload();
        }

        // Close modal when clicking outside
        document.getElementById('qr-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });
    </script>
</body>
</html>