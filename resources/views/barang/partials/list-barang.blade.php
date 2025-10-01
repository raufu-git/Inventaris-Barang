<x-table-list>
    <x-slot name="header">
        <tr>
            <th>#</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            <th>&nbsp;</th>
        </tr>
    </x-slot>

    @forelse ($barangs as $index => $barang)
        <tr>
            <td>{{ $barangs->firstItem() + $index }}</td>
            <td>{{ $barang->kode_barang }}</td>
            <td>{{ $barang->nama_barang }}</td>
            <td>{{ $barang->kategori->nama_kategori }}</td>
            <td>{{ $barang->lokasi->nama_lokasi }}</td>
                        <td>{{ $barang->jumlah_barang }}  {{ $barang->satuan }}</td>
            <td>
                @if ($barang->kondisi === 'Baik')
                    <span class="badge bg-info">{{ $barang->kondisi }}</span>
                @elseif ($barang->kondisi === 'Rusak Ringan')
                    <span class="badge bg-warning text-dark">{{ $barang->kondisi }}</span>
                @elseif ($barang->kondisi === 'Rusak Berat')
                    <span class="badge bg-danger">{{ $barang->kondisi }}</span>
                @else
                    <span class="badge bg-secondary">{{ $barang->kondisi }}</span>
                @endif
            </td>
            <td class="text-end">
                @can('manage barang')
                    <x-tombol-aksi href="{{ route('barang.show', $barang->id) }}" type="show" />
                    <x-tombol-aksi href="{{ route('barang.edit', $barang->id) }}" type="edit" />
                @endcan

                @can('delete barang')
                    <x-tombol-aksi href="{{ route('barang.destroy', $barang->id) }}" type="delete" />
                @endcan
            </td> 
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-danger">
                    Data barang belum tersedia.
                </div>
            </td>
        </tr>
    @endforelse
</x-table-list>