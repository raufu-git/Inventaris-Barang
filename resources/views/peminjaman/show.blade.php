<x-main-layout :title-page="__('Detail Peminjaman')">
    <div class="card my-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @include('peminjaman.partials.info-gambar-barang')
                </div>
                <div class="col-md">
                    @include('peminjaman.partials.info-data-gambar')
                </div>
            </div>
            <div class="mt-5">
                <x-tombol-kembali :href="route('peminjaman.index')" />
            </div>
        </div>
    </div>
</x-main-layout>