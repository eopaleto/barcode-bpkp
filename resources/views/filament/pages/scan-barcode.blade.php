    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                                <td class="font-semibold">Status Barang</td>
                                <td>:</td>
                                <td x-text="selected?.type ?? '-'"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Status</td>
                                <td>:</td>
                                <td>
                                    <template x-if="Number(selected?.status) === 1">
                                        <span class="flex items-center text-green-600">
                                            <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="h-6 w-6 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Sudah Diambil
                                        </span>
                                    </template>
                                    <template x-if="Number(selected?.status) !== 1">
                                        <span class="flex items-center text-yellow-600">
                                            <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="h-6 w-6 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Belum Diambil
                                        </span>
                                    </template>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Foto Koper</td>
                                <td>:</td>
                                <td>
                                    <template x-if="selected?.foto_koper?.length > 0">
                                        <button
                                            class="flex items-center space-x-1 text-blue-600 hover:text-blue-800 cursor-pointer"
                                            @click="showPreview = true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.183.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>Preview Gambar</span>
                                        </button>
                                    </template>

                                    <template x-if="!selected?.foto_koper || selected?.foto_koper?.length === 0">
                                        <span class="italic text-gray-500">Tidak ada foto</span>
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

            <!-- Modal Preview Foto -->
            <div x-show="showPreview" x-data="{
                currentIndex: 0,
                zoom: 1,
                offsetX: 0,
                offsetY: 0,
                dragging: false,
                startX: 0,
                startY: 0,
                reset() {
                    this.zoom = 1;
                    this.offsetX = 0;
                    this.offsetY = 0;
                }
            }"
                class="fixed inset-0 z-[9999] bg-black bg-opacity-90 flex flex-col justify-between select-none"
                x-transition>

                <!-- Tombol Close -->
                <button class="absolute top-6 right-6 text-white text-4xl font-bold hover:text-grey-600 transition z-50"
                    @click="showPreview = false; reset()">
                    &times;
                </button>

                <!-- Tombol Prev -->
                <button
                    class="absolute left-6 top-1/2 -translate-y-1/2 hover:bg-black/40
               text-white text-5xl font-bold px-4 py-3 rounded-full transition z-50"
                    @click="currentIndex = (currentIndex - 1 + selected.foto_koper.length) % selected.foto_koper.length; reset()">
                    &#10094;
                </button>

                <!-- Gambar Utama -->
                <div class="flex-1 flex items-center justify-center bg-black">
                    <img :src="`/storage/${selected.foto_koper[currentIndex]}`" alt="Foto Koper"
                        class="max-w-full max-h-[calc(100vh-140px)] object-contain cursor-grab transition-transform duration-150 ease-out"
                        :style="`transform: scale(${zoom}) translate(${offsetX}px, ${offsetY}px);`"
                        @wheel.prevent="zoom = Math.max(0.5, zoom + ($event.deltaY < 0 ? 0.1 : -0.1))"
                        @mousedown="dragging = true; startX = $event.clientX; startY = $event.clientY; $event.target.classList.add('cursor-grabbing')"
                        @mouseup="dragging = false; $event.target.classList.remove('cursor-grabbing')"
                        @mouseleave="dragging = false; $event.target.classList.remove('cursor-grabbing')"
                        @mousemove="if(dragging){ offsetX += ($event.clientX - startX)/zoom; offsetY += ($event.clientY - startY)/zoom; startX = $event.clientX; startY = $event.clientY }">
                </div>

                <!-- Tombol Next -->
                <button
                    class="absolute right-6 top-1/2 -translate-y-1/2 hover:bg-black/40
               text-white text-5xl font-bold px-4 py-3 rounded-full transition z-50"
                    @click="currentIndex = (currentIndex + 1) % selected.foto_koper.length; reset()">
                    &#10095;
                </button>

                <!-- Thumbnail Slider -->
                <div
                    class="flex gap-2 justify-center overflow-x-auto max-w-5xl mx-auto px-4 py-3 bg-black/60 rounded-t-lg h-[120px]">
                    <template x-for="(foto, i) in selected.foto_koper" :key="i">
                        <img :src="`/storage/${foto}`" @click="currentIndex = i; reset()"
                            class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 transition-all"
                            :class="currentIndex === i ?
                                'border-blue-500 ring-2 ring-blue-400' :
                                'border-transparent opacity-70 hover:opacity-100'" />
                    </template>
                </div>
            </div>

            <script>
                function previewFoto() {
                    return {
                        showPreview: false,
                        selected: {
                            foto_koper: []
                        },
                        currentIndex: 0,
                        zoom: 1,
                        offsetX: 0,
                        offsetY: 0,
                        dragging: false,
                        startX: 0,
                        startY: 0,
                    }
                }
            </script>

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
                    scanning: false,
                    showPreview: false,

                    async init() {
                        this.html5QrCode = new Html5Qrcode("reader");
                        const cameras = await Html5Qrcode.getCameras();
                        if (cameras && cameras.length) {
                            this.startScan();
                        } else {
                            alert("Kamera tidak ditemukan!");
                        }
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
                        }, async (decodedText) => {
                            if (this.scanning) return;
                            this.scanning = true;
                            beepSound.play();

                            try {
                                let res = await fetch('/api/pergi/' + decodedText);
                                if (res.ok) {
                                    let data = await res.json();
                                    this.selected = {
                                        ...data,
                                        type: 'Barang Pergi'
                                    };
                                } else {
                                    res = await fetch('/api/pulang/' + decodedText);
                                    if (res.ok) {
                                        let data = await res.json();
                                        this.selected = {
                                            ...data,
                                            type: 'Barang Pulang'
                                        };
                                    } else {
                                        alert('Data barcode tidak ditemukan!');
                                        this.scanning = false;
                                        return;
                                    }
                                }

                                Livewire.dispatch('open-modal', {
                                    id: 'confirm-ambil'
                                });
                            } catch (err) {
                                console.error(err);
                                alert('Terjadi kesalahan saat memproses barcode!');
                                this.scanning = false;
                                return;
                            }

                            this.html5QrCode.stop();
                        });
                    },

                    restart() {
                        this.scanning = false;
                        this.html5QrCode.clear();
                        document.getElementById("reader").innerHTML = "";
                        setTimeout(() => this.init(), 500);
                    },

                    async ambil() {
                        if (!this.selected) return;

                        let url = '';
                        if (this.selected.type === 'Barang Pergi') {
                            url = '/api/pergi/' + this.selected.kode + '/ambil';
                        } else if (this.selected.type === 'Barang Pulang') {
                            url = '/api/pulang/' + this.selected.kode + '/konfirmasi';
                        }

                        try {
                            await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            this.selected = null;
                            Livewire.dispatch('close-modal', {
                                id: 'confirm-ambil'
                            });
                            this.restart();
                            Livewire.dispatch('open-modal', {
                                id: 'success-modal'
                            });
                        } catch (err) {
                            console.error(err);
                            alert('Gagal mengkonfirmasi barang!');
                        }
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
