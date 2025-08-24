@vite(['resources/css/app.css', 'resources/js/app.js'])

<div
    x-data="{
        currentIndex: 0,
        fotos: @js($fotos),
        prevImage() {
            this.currentIndex = (this.currentIndex - 1 + this.fotos.length) % this.fotos.length
        },
        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.fotos.length
        }
    }"
    class="w-full flex flex-col items-center"
>

    <!-- Container Gambar Utama -->
    <div class="relative w-full max-w-2xl aspect-[4/3] rounded-lg flex items-center justify-center overflow-hidden">
        <img
            :src="'/storage/' + fotos[currentIndex]"
            class="max-w-full max-h-full object-contain"
        />

        <!-- Tombol Prev -->
        <button type="button" @click="prevImage"
            x-show="fotos.length > 1"
            class="absolute left-3 top-1/2 -translate-y-1/2 z-10
                   bg-gray-500/70 text-white rounded-full w-10 h-10 flex items-center justify-center
                   hover:bg-gray-700 transition text-2xl">
            ‹
        </button>

        <!-- Tombol Next -->
        <button type="button" @click="nextImage"
            x-show="fotos.length > 1"
            class="absolute right-3 top-1/2 -translate-y-1/2 z-10
                   bg-gray-500/70 text-white rounded-full w-10 h-10 flex items-center justify-center
                   hover:bg-gray-700 transition text-2xl">
            ›
        </button>
    </div>

    <!-- Thumbnail -->
    <div class="flex justify-center gap-4 overflow-x-auto mt-6 pb-2 w-full max-w-2xl">
        <template x-for="(foto, i) in fotos" :key="i">
            <img
                x-show="fotos.length > 1"
                :src="'/storage/' + foto"
                @click="currentIndex = i"
                class="w-24 h-24 object-cover rounded-md cursor-pointer border-2 transition-all"
                :class="currentIndex === i
                    ? 'border-blue-500 ring-2 ring-blue-400'
                    : 'border-gray-300 opacity-70 hover:opacity-100'"
            />
        </template>
    </div>
</div>
