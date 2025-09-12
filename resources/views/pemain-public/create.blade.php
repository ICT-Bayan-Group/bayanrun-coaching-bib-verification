<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Pemain Bola</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Form Pendaftaran Pemain Bola</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('pemain.public.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Dropdown Sekolah Bola -->
            <div>
                <label class="block mb-1 font-medium">Sekolah Bola</label>
                <select name="sekolah_bola_id" class="w-full border-gray-300 rounded p-2">
                    <option value="">-- Pilih Sekolah Bola --</option>
                    @foreach($sekolahBolas as $sekolah)
                        <option value="{{ $sekolah->id }}">{{ $sekolah->nama }}</option>
                    @endforeach
                </select>
                @error('sekolah_bola_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Input Nama -->
            <div>
                <label class="block mb-1 font-medium">Nama Pemain</label>
                <input type="text" name="nama" class="w-full border-gray-300 rounded p-2" placeholder="Masukkan nama pemain">
                @error('nama') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Umur Kategori -->
            <div>
                <label class="block mb-1 font-medium">Kategori Umur</label>
                <div class="flex space-x-2">
                    <label>
                        <input type="radio" name="umur_kategori" value="U-12"> U-12
                    </label>
                    <label>
                        <input type="radio" name="umur_kategori" value="U-15"> U-15
                    </label>
                    <label>
                        <input type="radio" name="umur_kategori" value="U-18"> U-18
                    </label>
                </div>
                @error('umur_kategori') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">
                Daftar
            </button>
        </form>
    </div>
</body>
</html>
