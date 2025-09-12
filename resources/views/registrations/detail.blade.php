<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peserta - {{ $peserta->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Peserta</h1>
                        <p class="text-gray-600">{{ $peserta->nama_lengkap }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.peserta') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                            ← Kembali
                        </a>
                        <button onclick="printDetail()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            🖨️ Print
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Informasi Peserta</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $peserta->nama_lengkap }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Lari</label>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $peserta->kategori_lari }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">{{ $peserta->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <p class="text-gray-900">{{ $peserta->telepon }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor BIB</label>
                                <p class="text-2xl font-bold text-blue-600">{{ $peserta->nomor_bib }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                    @if($peserta->status === 'terdaftar') bg-yellow-100 text-yellow-800
                                    @elseif($peserta->status === 'konfirmasi') bg-blue-100 text-blue-800
                                    @elseif($peserta->status === 'hadir') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($peserta->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Timeline -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Timeline Pendaftaran</h2>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <span class="text-white text-sm">✓</span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Pendaftaran berhasil</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $peserta->created_at->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                @if($peserta->status !== 'terdaftar')
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <span class="text-white text-sm">📋</span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Status diupdate: {{ ucfirst($peserta->status) }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $peserta->updated_at->format('d M Y, H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($peserta->waktu_checkin)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                    <span class="text-white text-sm">🏃‍♂️</span>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Check-in event</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $peserta->waktu_checkin ? \Carbon\Carbon::parse($peserta->waktu_checkin)->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- QR Code -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">QR Code</h3>
                        @if($peserta->qr_code_path)
                            <div class="text-center">
                                <img src="{{ $peserta->qr_code_url }}" 
                                     alt="QR Code {{ $peserta->nama_lengkap }}" 
                                     class="w-48 h-48 mx-auto border rounded-lg mb-4">
                                <p class="text-sm text-gray-600 mb-4">Scan untuk verifikasi peserta</p>
                                <button onclick="downloadQR()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">
                                    📱 Download QR Code
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">QR Code tidak tersedia</p>
                                <button onclick="generateQR({{ $peserta->id }})" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                                    🔄 Generate QR Code
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Quick Actions</h3>
                        <div class="space-y-3">
                            <button onclick="updateStatus()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-left">
                                ✏️ Update Status
                            </button>
                            <button onclick="sendWhatsApp()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-left">
                                💬 Kirim WhatsApp
                            </button>
                            <button onclick="markCheckin()" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-left">
                                ✅ Mark Check-in
                            </button>
                            <button onclick="exportPDF()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-left">
                                📄 Export PDF
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Statistik</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Waktu Daftar:</span>
                                <span class="text-sm font-medium">{{ $peserta->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Token QR:</span>
                                <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">
                                    {{ substr($peserta->qr_token, 0, 8) }}...
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">ID Peserta:</span>
                                <span class="text-sm font-medium">#{{ $peserta->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Update Status Peserta</h3>
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="radio" name="new-status" value="terdaftar" class="mr-3">
                    <span>Terdaftar</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="new-status" value="konfirmasi" class="mr-3">
                    <span>Konfirmasi</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="new-status" value="hadir" class="mr-3">
                    <span>Hadir</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="new-status" value="tidak_hadir" class="mr-3">
                    <span>Tidak Hadir</span>
                </label>
            </div>
            <div class="flex space-x-3 mt-6">
                <button onclick="closeStatusModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg">
                    Batal
                </button>
                <button onclick="saveStatus()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <script>
        function updateStatus() {
            document.getElementById('status-modal').classList.remove('hidden');
            document.getElementById('status-modal').classList.add('flex');
            
            // Set current status as selected
            const currentStatus = '{{ $peserta->status }}';
            const radio = document.querySelector(`input[value="${currentStatus}"]`);
            if (radio) radio.checked = true;
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
            document.getElementById('status-modal').classList.remove('flex');
        }

        function saveStatus() {
            const selectedStatus = document.querySelector('input[name="new-status"]:checked');
            if (selectedStatus) {
                // Implement status update API call
                alert(`Status akan diupdate ke: ${selectedStatus.value}`);
                closeStatusModal();
            }
        }

        function sendWhatsApp() {
            const phone = '{{ $peserta->formatted_phone }}';
            const message = `Halo {{ $peserta->nama_lengkap }}, ini adalah konfirmasi pendaftaran Anda di Event Lari Bayan Run 2025. Nomor BIB: {{ $peserta->nomor_bib }}`;
            
            // Open WhatsApp Web
            const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        function markCheckin() {
            if (confirm('Mark peserta ini sebagai sudah check-in?')) {
                // Implement check-in API call
                alert('Peserta berhasil di-check-in!');
            }
        }

        function downloadQR() {
            const qrUrl = '{{ $peserta->qr_code_url ?? "" }}';
            if (qrUrl) {
                const link = document.createElement('a');
                link.href = qrUrl;
                link.download = 'QR-{{ $peserta->nomor_bib }}-{{ $peserta->nama_lengkap }}.png';
                link.click();
            }
        }

        function generateQR(pesertaId) {
            // Implement QR generation API call
            alert('Generate QR Code untuk peserta ID: ' + pesertaId);
        }

        function printDetail() {
            window.print();
        }

        function exportPDF() {
            // Implement PDF export
            alert('Export PDF functionality');
        }

        // Close modal when clicking outside
        document.getElementById('status-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</body>
</html>