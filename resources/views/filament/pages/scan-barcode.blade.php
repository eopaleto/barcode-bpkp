<x-filament::page>
    <div x-data="scanBarcode()" x-init="init()" class="flex flex-col items-center justify-center">
        <!-- Area Scanner -->
        <div id="reader" class="w-full max-w-md mx-auto rounded-lg shadow-lg overflow-hidden"></div>
        <audio id="beepSound" src="{{ asset('sounds/beep.mp3') }}"></audio>

        <!-- Modal Konfirmasi -->
        <x-filament::modal id="confirm-ambil" width="4xl" x-on:close="closeModal()">
            <x-slot name="header">
                <h2 class="text-xl font-bold text-gray-800 border-b pb-2">
                    Konfirmasi Ambil Barang
                </h2>
            </x-slot>

            <div class="py-6">
                <table class="w-full text-lg">
                    <tbody class="space-y-2">
                        <tr>
                            <td class="font-semibold w-40">Kode</td>
                            <td class="w-2">:</td>
                            <td class="font-bold" x-text="selected?.kode ?? '-'"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Nama</td>
                            <td>:</td>
                            <td x-text="selected?.pegawai?.nama ?? '-'"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold">NIP</td>
                            <td>:</td>
                            <td x-text="selected?.pegawai?.nip_baru ?? '-'"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Unit Kerja</td>
                            <td>:</td>
                            <td x-text="selected?.pegawai?.unit_kerja ?? '-'"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Jabatan</td>
                            <td>:</td>
                            <td x-text="selected?.pegawai?.jabatan ?? '-'"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-green-600">Status</td>
                            <td>:</td>
                            <td>
                                <template x-if="Number(selected?.status) === 1">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="h-6 w-6 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        Sudah Diambil
                                    </span>
                                </template>
                                <template x-if="Number(selected?.status) !== 1">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="h-6 w-6 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        Belum Diambil
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-slot name="footer">
                <div class="w-full flex justify-end space-x-3">
                    <x-filament::button class="me-3" color="gray" x-on:click="closeModal">
                        Batal
                    </x-filament::button>
                    <x-filament::button color="success" x-on:click="ambil" x-show="selected?.status != 1">
                        Ya, Konfirmasi
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>

        <!-- Modal Success -->
        <x-filament::modal id="success-modal" width="md">
            <x-slot name="header">
                <h2 class="text-xl font-bold text-green-600">
                    Berhasil!
                </h2>
            </x-slot>

            <div class="py-6 text-lg text-center text-gray-700">
                Barang berhasil dikonfirmasi.
            </div>

            <x-slot name="footer">
                <div class="w-200 flex justify-center">
                    <x-filament::button color="success" x-on:click="closeSuccess">
                        OK
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function scanBarcode() {
            return {
                selected: null,
                html5QrCode: null,
                init() {
                    this.html5QrCode = new Html5Qrcode("reader");
                    Html5Qrcode.getCameras().then(cameras => {
                        if (cameras && cameras.length) {
                            this.startScan();
                        } else {
                            alert("Kamera tidak ditemukan!");
                        }
                    });
                },
                startScan() {
                    const beepSound = document.getElementById("beepSound");
                    this.html5QrCode.start({
                            facingMode: "environment"
                        }, {
                            fps: 60,
                            qrbox: {
                                width: 250,
                                height: 250
                            }
                        },
                        (decodedText) => {
                            beepSound.play();
                            fetch('/api/pergi/' + decodedText)
                                .then(res => res.json())
                                .then(data => {
                                    this.selected = data;
                                    Livewire.dispatch('open-modal', {
                                        id: 'confirm-ambil'
                                    });
                                });
                            this.html5QrCode.stop();
                        }
                    );
                },
                restart() {
                    this.html5QrCode.clear();
                    document.getElementById("reader").innerHTML = "";
                    this.init();
                },
                ambil() {
                    fetch('/api/pergi/' + this.selected.kode + '/ambil', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(() => {
                            this.selected = null;
                            Livewire.dispatch('close-modal', {
                                id: 'confirm-ambil'
                            });
                            this.restart();
                            Livewire.dispatch('open-modal', {
                                id: 'success-modal'
                            });
                        });
                },
                closeModal() {
                    this.selected = null;
                    Livewire.dispatch('close-modal', {
                        id: 'confirm-ambil'
                    });
                    this.restart();
                },
                closeSuccess() {
                    Livewire.dispatch('close-modal', {
                        id: 'success-modal'
                    });
                    this.restart();
                }
            }
        }
    </script>
</x-filament::page>
