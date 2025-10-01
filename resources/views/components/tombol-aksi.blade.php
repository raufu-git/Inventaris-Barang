@props(['href' => '#', 'type', 'id' => null, 'nama_peminjam' => null, 'barang' => null, 'jumlah' => null, 'keterangan' => null])

@switch($type)
    @case('show')
        <a href="{{ $href }}" class="btn btn-sm btn-info" title="Detail">
            <i class="bi bi-card-list"></i>
        </a>
    @break

    @case('edit')
        <a href="{{ $href }}" class="btn btn-sm btn-warning" title="Edit">
            <i class="bi bi-pencil-square"></i>
        </a>
    @break

    @case('delete')
        <button type="button" class="btn btn-sm btn-danger"
            onclick="confirmDelete('{{ $href }}')" title="Hapus">
            <i class="bi bi-x-circle"></i>
        </button>
    @break

    @case('return')
        <button type="button" class="btn btn-sm btn-success" title="Kembalikan"
            onclick="confirmKembalikan({{ $id }}, '{{ $nama_peminjam }}', '{{ $barang }}', '{{ $jumlah }}', '{{ $keterangan }}')">
            <i class="bi bi-arrow-counterclockwise"></i>
        </button>

        <form id="form-kembalikan-{{ $id }}" action="{{ route('peminjaman.kembalikan', $id) }}" method="POST" style="display:none;">
            @csrf
            @method('PATCH')
        </form>
    @break
@endswitch
