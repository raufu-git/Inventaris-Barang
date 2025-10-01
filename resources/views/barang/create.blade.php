<x-main-layout :title-page="__('Tambah Barang')">
    <form class="card" action="{{ route('barang.store') }}" method="post" enctype="multipart/form-data">
        <div class="card-body">
            @include('barang.partials._form')
        </div>
    </form>
</x-main-layout>
