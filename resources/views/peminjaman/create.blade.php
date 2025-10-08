<x-main-layout :title-page="__('Tambah Peminjaman')">
  <div class="row">
    <form class="card col-lg-8" action="{{ route('peminjaman.store') }}" method="POST">
      @csrf
      <div class="card-body">
        @include('peminjaman.partials._form')
      </div>
    </form>
  </div>
</x-main-layout>
