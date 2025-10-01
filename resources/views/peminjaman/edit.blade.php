<x-main-layout :title-page="__('Edit Peminjaman')">
  <div class="row">
    <form class="card col-lg-6" action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body">
        @include('peminjaman.partials._form', ['update' => true])
      </div>
    </form>
  </div>
</x-main-layout>
