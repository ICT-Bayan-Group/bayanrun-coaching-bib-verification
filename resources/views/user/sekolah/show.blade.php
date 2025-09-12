<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayan Soccer Dashboard - {{ $sekolah->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="ico" href="{{ asset('logo.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sidebar-active {
            background-color: #f3f4f6;
            border-right: 3px solid #f59e0b;
        }
        
        .btn-orange {
            background-color: #f59e0b;
        }
        
        .btn-orange:hover {
            background-color: #d97706;
        }
        
        .btn-green {
            background-color: #10b981;
        }
        
        .btn-green:hover {
            background-color: #059669;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .table-hover:hover {
            background-color: #f9fafb;
        }
        
        .action-btn {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .action-btn.view {
            background-color: #6b7280;
            color: white;
        }
        
        .action-btn.edit {
            background-color: #f59e0b;
            color: white;
        }
        
        .action-btn.delete {
            background-color: #ef4444;
            color: white;
        }
        
        .action-btn:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }

        .alert-slide-down {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .badge-warning { background-color: #fef3c7; color: #d97706; }
        .badge-success { background-color: #d1fae5; color: #059669; }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }

        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #f59e0b;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <!-- Logo Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <img src="https://rec-data.kalibrr.com/www.kalibrr.com/logos/GKTW5JUNVCCLNZFJFEE7ESSU26UQKVR5QKN7E65J-64ab6a30.png" 
                        alt="Bayan Logo" 
                        class="w-14 h-18 object-cover">
                    <div>
                        <h2 class="text-lg font-bold text-orange-600">SOCCER CLINIC</h2>
                        <p class="text-sm text-gray-500">{{ $sekolah->nama }}</p>
                    </div>
                </div>
            </div>     
            <!-- Navigation -->
            <nav class="mt-4">
                <div class="px-4 py-2">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Menu</h3>
                </div>
                <ul class="mt-2">
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-gray-700 bg-gray-100 border-r-4 border-orange-500 font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Pemain SSB
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <nav class="flex items-center space-x-2 text-sm font-medium text-orange-600">
                                <span>Daftar Pemain SSB</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="text-orange-600 font-medium">List</span>
                            </nav>
                            <h1 class="text-2xl font-bold text-gray-900 mt-1">Daftar Pemain SSB</h1>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-6">
                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Alert Informasi Kuota yang sudah diupdate -->
                <div id="quotaAlert" class="mb-6 bg-gradient-to-r from-orange-50 to-green-50 border border-blue-200 rounded-lg p-6 alert-slide-down">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-blue-800 mb-2">
                                <i class="fas fa-futbol mr-2"></i>Informasi Kuota SSB {{ $sekolah->nama }}
                            </h3>
                            <div class="text-blue-700 mb-4">
                                @if($kuotaData['has_quota'])
                                    <p class="mb-2">
                                        Kuota yang diberikan: <strong class="text-blue-800">{{ $kuotaData['total'] }} Orang</strong>
                                    </p>
                                    <p class="mb-4 text-sm">
                                        Pemain terdaftar: <strong class="text-green-800">{{ $kuotaData['current_counts']['total'] }} Orang dari {{ $kuotaData['total'] }} Orang</strong>
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-600">Kelompok 7-8 Tahun</span>
                                                <span class="badge badge-info">{{ $kuotaData['7-8'] }} kuota</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs mb-2">
                                                <span class="text-gray-500">Terdaftar: {{ $kuotaData['current_counts']['7-8'] }}</span>
                                                <span class="text-green-600 font-medium">Sisa: {{ $kuotaData['remaining']['7-8'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-transparent h-2 rounded-full transition-all duration-300" 
                                                    style="width: {{ $kuotaData['percentage']['7-8'] }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-600">Kelompok 9-10 Tahun</span>
                                                <span class="badge badge-warning">{{ $kuotaData['9-10'] }} kuota</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs mb-2">
                                                <span class="text-gray-500">Terdaftar: {{ $kuotaData['current_counts']['9-10'] }}</span>
                                                <span class="text-green-600 font-medium">Sisa: {{ $kuotaData['remaining']['9-10'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-transparent h-2 rounded-full transition-all duration-300" 
                                                    style="width: {{ $kuotaData['percentage']['9-10'] }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-600">Kelompok 11-12 Tahun</span>
                                                <span class="badge badge-success">{{ $kuotaData['11-12'] }} kuota</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs mb-2">
                                                <span class="text-gray-500">Terdaftar: {{ $kuotaData['current_counts']['11-12'] }}</span>
                                                <span class="text-green-600 font-medium">Sisa: {{ $kuotaData['remaining']['11-12'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-transparent h-2 rounded-full transition-all duration-300" 
                                                    style="width: {{ $kuotaData['percentage']['11-12'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Alert untuk kategori yang hampir penuh atau penuh -->
                                    @php
                                        $alerts = [];
                                        foreach(['7-8', '9-10', '11-12'] as $cat) {
                                            if($kuotaData['percentage'][$cat] >= 100) {
                                                $alerts[] = "Kategori {$cat} tahun sudah melebihi batas";
                                            } elseif($kuotaData['percentage'][$cat] >= 80) {
                                                $alerts[] = "Kategori {$cat} tahun hampir penuh ({$kuotaData['percentage'][$cat]}%)";
                                            }
                                        }
                                    @endphp
                                    
                                    @if(count($alerts) > 0)
                                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                                <div class="text-yellow-800 text-sm">
                                                    <strong>Perhatian:</strong>
                                                    <ul class="list-disc list-inside mt-1">
                                                        @foreach($alerts as $alert)
                                                            <li>{{ $alert }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                                            <div class="text-yellow-800">
                                                <strong>Kuota belum ditetapkan</strong>
                                                <p class="text-sm mt-1">Admin belum menetapkan kuota untuk SSB Anda. Hubungi admin untuk informasi lebih lanjut.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <p class="mt-3 text-sm">
                                    <i class="fas fa-users-cog mr-1"></i>
                                    Silakan Anda memanajemen pemain SSB Anda dengan bijak sesuai kategori umur yang tersedia.
                                </p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <button onclick="refreshQuota()" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                        <i class="fas fa-refresh mr-1"></i>Refresh Data
                                    </button>
                                    @if($kuotaData['has_quota'])
                                        <span class="text-gray-400">•</span>
                                        <span class="text-xs text-gray-500">Diperbarui: {{ $sekolah->kuotaSekolah->updated_at ?? 'Belum diatur' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                <div id="successAlert" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 alert-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-green-800">{{ session('success') }}</span>
                        <button onclick="closeSuccessAlert()" class="ml-auto text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div id="errorAlert" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 alert-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-red-800">{{ session('error') }}</span>
                        <button onclick="closeErrorAlert()" class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Table Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <input type="text" id="searchInput" placeholder="Cari nama pemain..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                         <!--    <button onclick="openAddModal()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Tambah Pemain
                            </button>-->
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="bg-white p-6 rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full border border-gray-200 rounded-lg">
                                <thead>
                                    <tr class="bg-gray-100 text-left text-gray-600">
                                        <th class="p-3">No</th>
                                        <th class="p-3">Nama</th>
                                        <th class="p-3">Umur</th>
                                        <th class="p-3">Kategori</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="playerTable">
                                    @forelse($sekolah->pemainBolas as $index => $pemain)
                                    <tr class="border-t hover:bg-gray-50 table-hover" data-player-id="{{ $pemain->id }}">
                                        <td class="p-3">{{ $index + 1 }}</td>
                                        <td class="p-3 font-medium">{{ $pemain->nama }}</td>
                                        <td class="p-3">{{ $pemain->umur }} tahun</td>
                                        <td class="p-3">
                                            <span class="badge 
                                                @if($pemain->umur_kategori == '7-8') badge-info 
                                                @elseif($pemain->umur_kategori == '9-10') badge-warning 
                                                @else badge-success @endif">
                                                {{ $pemain->umur_kategori }} tahun
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <button onclick="editPlayer({{ $pemain->id }})" class="action-btn edit">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </button>
                                                <button onclick="deletePlayer({{ $pemain->id }}, '{{ $pemain->nama }}')" class="action-btn delete">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr id="emptyRow">
                                        <td colspan="5" class="p-8 text-center text-gray-500">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium">Belum ada pemain</p>
                                            <p class="text-sm">Mulai tambahkan pemain untuk SSB Anda</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Player Modal -->
    <div id="editPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-overlay">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md modal-content">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Data Pemain</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editPlayerForm" class="p-6">
                    <div class="mb-4">
                        <label for="edit_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Pemain</label>
                        <input type="text" id="edit_nama" name="nama" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label for="edit_umur" class="block text-sm font-medium text-gray-700 mb-2">Umur (tahun)</label>
                        <input type="number" id="edit_umur" name="umur" required min="7" max="12"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Umur</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="edit_umur_kategori" value="7-8" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    7-8 Tahun
                                </span>
                            </label>
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="edit_umur_kategori" value="9-10" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    9-10 Tahun
                                </span>
                            </label>
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="edit_umur_kategori" value="11-12" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    11-12 Tahun
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                            Batal
                        </button>
                        <button type="submit" id="updatePlayerBtn"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition duration-200">
                            <span class="loading-text">Update Pemain</span>
                            <span class="loading-spinner-container hidden">
                                <div class="loading-spinner inline-block"></div>
                                <span class="ml-2">Memperbarui...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Player Modal -->
    <div id="addPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-overlay">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md modal-content">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Pemain Baru</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="addPlayerForm" class="p-6">
                    <div class="mb-4">
                        <label for="add_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Pemain</label>
                        <input type="text" id="add_nama" name="nama" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label for="add_umur" class="block text-sm font-medium text-gray-700 mb-2">Umur (tahun)</label>
                        <input type="number" id="add_umur" name="umur" required min="7" max="12"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Umur</label>
                        <div class="flex flex-wrap gap-2">
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="add_umur_kategori" value="7-8" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    7-8 Tahun
                                </span>
                            </label>
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="add_umur_kategori" value="9-10" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    9-10 Tahun
                                </span>
                            </label>
                            <label class="flex-1 flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="add_umur_kategori" value="11-12" class="hidden peer" required>
                                <span class="w-full text-center text-xs px-2 py-2 border-2 border-gray-300 rounded-lg peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 transition duration-300">
                                    11-12 Tahun
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                            Batal
                        </button>
                        <button type="submit" id="addPlayerBtn"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition duration-200">
                            <span class="loading-text">Tambah Pemain</span>
                            <span class="loading-spinner-container hidden">
                                <div class="loading-spinner inline-block"></div>
                                <span class="ml-2">Menambahkan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Player Modal -->
    <div id="addPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-overlay">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md modal-content">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Pemain Baru</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="addPlayerForm" class="p-6">
                    <div class="mb-4">
                        <label for="add_nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Pemain</label>
                        <input type="text" id="add_nama" name="nama" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                    <div class="mb-6">
                        <label for="add_umur" class="block text-sm font-medium text-gray-700 mb-2">Umur</label>
                        <select id="add_umur" name="umur" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">Pilih Umur</option>
                            <option value="7">7 tahun</option>
                            <option value="8">8 tahun</option>
                            <option value="9">9 tahun</option>
                            <option value="10">10 tahun</option>
                            <option value="11">11 tahun</option>
                            <option value="12">12 tahun</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                            Batal
                        </button>
                        <button type="submit" id="addPlayerBtn"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition duration-200">
                            <span class="loading-text">Tambah Pemain</span>
                            <span class="loading-spinner-container hidden">
                                <div class="loading-spinner inline-block"></div>
                                <span class="ml-2">Menambahkan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentEditingPlayerId = null;
        const userToken = '{{ $sekolah->user_token }}';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Show alert function
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
            const iconClass = type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500';
            
            const alertHTML = `
                <div class="mb-6 ${alertClass} border rounded-lg p-4 alert-slide-down">
                    <div class="flex items-center">
                        <i class="${iconClass} mr-3"></i>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-current hover:opacity-70">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            alertContainer.innerHTML = alertHTML;
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('div');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Auto-detect kategori umur berdasarkan umur (seperti form pemain)
        function setupUmurAutoDetect(umurInputId, radioName) {
            document.getElementById(umurInputId).addEventListener('input', function() {
                const umur = parseInt(this.value);
                const radioButtons = document.querySelectorAll(`input[name="${radioName}"]`);
                
                // Reset all radio buttons
                radioButtons.forEach(radio => radio.checked = false);
                
                // Auto select kategori berdasarkan umur
                if (umur >= 7 && umur <= 8) {
                    const radio7_8 = document.querySelector(`input[name="${radioName}"][value="7-8"]`);
                    if (radio7_8) radio7_8.checked = true;
                } else if (umur >= 9 && umur <= 10) {
                    const radio9_10 = document.querySelector(`input[name="${radioName}"][value="9-10"]`);
                    if (radio9_10) radio9_10.checked = true;
                } else if (umur >= 11 && umur <= 12) {
                    const radio11_12 = document.querySelector(`input[name="${radioName}"][value="11-12"]`);
                    if (radio11_12) radio11_12.checked = true;
                }
            });
        }

        // Open add modal
        function openAddModal() {
            document.getElementById('addPlayerModal').classList.remove('hidden');
            document.getElementById('add_nama').focus();
            // Setup auto-detect untuk add modal
            setupUmurAutoDetect('add_umur', 'add_umur_kategori');
        }

        // Close add modal
        function closeAddModal() {
            document.getElementById('addPlayerModal').classList.add('hidden');
            document.getElementById('addPlayerForm').reset();
        }

        // Open edit modal
        async function editPlayer(playerId) {
            currentEditingPlayerId = playerId;
            
            try {
                const response = await fetch(`/user/${userToken}/edit-pemain/${playerId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('edit_nama').value = data.data.nama;
                    document.getElementById('edit_umur').value = data.data.umur;
                    
                    // Auto select kategori radio button berdasarkan umur
                    const kategori = data.data.umur_kategori;
                    const radioButtons = document.querySelectorAll('input[name="edit_umur_kategori"]');
                    radioButtons.forEach(radio => {
                        radio.checked = radio.value === kategori;
                    });
                    
                    document.getElementById('editPlayerModal').classList.remove('hidden');
                    document.getElementById('edit_nama').focus();
                    
                    // Setup auto-detect untuk edit modal
                    setupUmurAutoDetect('edit_umur', 'edit_umur_kategori');
                } else {
                    showAlert('Gagal mengambil data pemain', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat mengambil data', 'error');
            }
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editPlayerModal').classList.add('hidden');
            document.getElementById('editPlayerForm').reset();
            currentEditingPlayerId = null;
        }

        // Delete player
        async function deletePlayer(playerId, playerName) {
            if (confirm(`Yakin ingin menghapus pemain "${playerName}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`)) {
                try {
                    const response = await fetch(`/user/${userToken}/hapus-pemain/${playerId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Remove row from table
                        const row = document.querySelector(`tr[data-player-id="${playerId}"]`);
                        if (row) {
                            row.remove();
                        }
                        // Refresh page after delay to update quota display
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat menghapus data', 'error');
                }
            }
        }

        // Handle add player form submission (seperti form pemain di referensi)
        document.getElementById('addPlayerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('addPlayerBtn');
            const loadingText = submitBtn.querySelector('.loading-text');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner-container');
            
            // Show loading state
            loadingText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            submitBtn.disabled = true;
            
            // Get form data
            const nama = document.getElementById('add_nama').value;
            const umur = document.getElementById('add_umur').value;
            
            // Validasi kategori umur sesuai dengan umur (seperti di referensi)
            const umurInt = parseInt(umur);
            let kategori = '';
            if (umurInt >= 7 && umurInt <= 8) {
                kategori = '7-8';
            } else if (umurInt >= 9 && umurInt <= 10) {
                kategori = '9-10';
            } else if (umurInt >= 11 && umurInt <= 12) {
                kategori = '11-12';
            }
            
            if (!kategori) {
                showAlert('Umur tidak valid! Harus antara 7-12 tahun', 'error');
                loadingText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
                submitBtn.disabled = false;
                return;
            }
            
            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('umur', umur);
            
            try {
                const response = await fetch(`/user/${userToken}/tambah-pemain`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeAddModal();
                    // Refresh page to show new data and update quota
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Gagal menambahkan pemain', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat menambahkan data', 'error');
            } finally {
                // Reset loading state
                loadingText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
                submitBtn.disabled = false;
            }
        });

        // Handle edit player form submission
        document.getElementById('editPlayerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!currentEditingPlayerId) return;
            
            const submitBtn = document.getElementById('updatePlayerBtn');
            const loadingText = submitBtn.querySelector('.loading-text');
            const loadingSpinner = submitBtn.querySelector('.loading-spinner-container');
            
            // Show loading state
            loadingText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            submitBtn.disabled = true;
            
            // Get form data
            const nama = document.getElementById('edit_nama').value;
            const umur = document.getElementById('edit_umur').value;
            
            // Validasi kategori umur sesuai dengan umur (seperti di referensi)
            const umurInt = parseInt(umur);
            let kategori = '';
            if (umurInt >= 7 && umurInt <= 8) {
                kategori = '7-8';
            } else if (umurInt >= 9 && umurInt <= 10) {
                kategori = '9-10';
            } else if (umurInt >= 11 && umurInt <= 12) {
                kategori = '11-12';
            }
            
            if (!kategori) {
                showAlert('Umur tidak valid! Harus antara 7-12 tahun', 'error');
                loadingText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
                submitBtn.disabled = false;
                return;
            }
            
            const formData = new FormData();
            formData.append('nama', nama);
            formData.append('umur', umur);
            
            try {
                const response = await fetch(`/user/${userToken}/update-pemain/${currentEditingPlayerId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeEditModal();
                    // Refresh page to show updated data and quota
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Gagal memperbarui pemain', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat memperbarui data', 'error');
            } finally {
                // Reset loading state
                loadingText.classList.remove('hidden');
                loadingSpinner.classList.add('hidden');
                submitBtn.disabled = false;
            }
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#playerTable tr');
            let visibleCount = 0;

            rows.forEach(function(row) {
                if (row.id === 'emptyRow') return;
                
                const nama = row.querySelector('td:nth-child(2)');
                if (nama) {
                    const namaText = nama.textContent.toLowerCase();
                    if (namaText.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Show/hide empty state
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) {
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });

        // Close modal when clicking outside
        document.getElementById('editPlayerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.getElementById('addPlayerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeAddModal();
            }
        });
    </script>
</body>
</html>