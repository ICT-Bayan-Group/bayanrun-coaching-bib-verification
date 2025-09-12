<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemain - {{ $sekolah->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="https://rec-data.kalibrr.com/www.kalibrr.com/logos/GKTW5JUNVCCLNZFJFEE7ESSU26UQKVR5QKN7E65J-64ab6a30.png" 
                     alt="Bayan Logo" 
                     class="mx-auto w-16 h-20 object-cover mb-4">
                <h2 class="text-3xl font-bold text-gray-900">Edit Pemain</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $sekolah->nama }}</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-8">
                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <h3 class="text-red-800 font-medium">Terdapat kesalahan:</h3>
                    </div>
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('user.pemain.update', [$sekolah->user_token, $pemain->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Nama Pemain -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-gray-400"></i>Nama Pemain
                            </label>
                            <input type="text" 
                                   id="nama" 
                                   name="nama" 
                                   value="{{ old('nama', $pemain->nama) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('nama') border-red-500 @enderror"
                                   placeholder="Masukkan nama pemain"
                                   required>
                            @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Umur -->
                        <div>
                            <label for="umur" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-birthday-cake mr-2 text-gray-400"></i>Umur
                            </label>
                            <select id="umur" 
                                    name="umur" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('umur') border-red-500 @enderror"
                                    onchange="updateKategori()"
                                    required>
                                <option value="">Pilih umur</option>
                                @for($i = 7; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('umur', $pemain->umur) == $i ? 'selected' : '' }}>
                                    {{ $i }} tahun
                                </option>
                                @endfor
                            </select>
                            @error('umur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori Umur (Auto-filled) -->
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-2 text-gray-400"></i>Kategori Umur
                            </label>
                            <div id="kategoriDisplay" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                {{ $pemain->umur_kategori }} tahun
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Kategori akan otomatis terisi berdasarkan umur yang dipilih</p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="submit" 
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:ring-4 focus:ring-orange-300">
                                <i class="fas fa-save mr-2"></i>Update Pemain
                            </button>
                            <a href="{{ route('user.pemain.index', $sekolah->user_token) }}" 
                               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 focus:ring-4 focus:ring-gray-300 text-center">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium">Informasi:</p>
                        <p>Pemain akan otomatis dikategorikan berdasarkan umur yang dipilih (7-8, 9-10, atau 11-12 tahun).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateKategori() {
            const umur = parseInt(document.getElementById('umur').value);
            const kategoriDisplay = document.getElementById('kategoriDisplay');
            
            if (umur >= 7 && umur <= 8) {
                kategoriDisplay.textContent = '7-8 tahun';
                kategoriDisplay.className = 'w-full px-3 py-2 bg-blue-100 border border-blue-300 rounded-lg text-blue-800';
            } else if (umur >= 9 && umur <= 10) {
                kategoriDisplay.textContent = '9-10 tahun';
                kategoriDisplay.className = 'w-full px-3 py-2 bg-yellow-100 border border-yellow-300 rounded-lg text-yellow-800';
            } else if (umur >= 11 && umur <= 12) {
                kategoriDisplay.textContent = '11-12 tahun';
                kategoriDisplay.className = 'w-full px-3 py-2 bg-green-100 border border-green-300 rounded-lg text-green-800';
            } else {
                kategoriDisplay.textContent = 'Pilih umur terlebih dahulu';
                kategoriDisplay.className = 'w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700';
            }
        }

        // Initialize kategori display on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateKategori();
        });
    </script>
</body>
</html>