<div class="row">
    @php
        $kartus = [
            [
                'text' => 'TOTAL BARANG',
                'total' => $jumlahBarang,
                'route' => 'barang.index',
                'icon' => 'bi-box-seam',
                'color' => 'primary',
            ],
            [
                'text' => 'TOTAL KATEGORI',
                'total' => $jumlahKategori,
                'route' => 'kategori.index',
                'icon' => 'bi-tags',
                'color' => 'warning',
            ],
            [
                'text' => 'TOTAL LOKASI',
                'total' => $jumlahLokasi,
                'route' => 'lokasi.index',
                'icon' => 'bi-geo-alt',
                'color' => 'success',
            ],
            [
                'text' => 'TOTAL PEMINJAM',
                'total' => $jumlahPeminjaman,
                'route' => 'peminjaman.index',
                'icon' => 'bi-box-arrow-up',
                'color' => 'secondary',
            ],
            [
                'text' => 'TOTAL USER',
                'total' => $jumlahUser,
                'route' => 'user.index',
                'icon' => 'bi-people',
                'color' => 'danger',
                'role' => 'admin',
            ],
        ];
    @endphp

    @foreach ($kartus as $kartu)
        @php
            extract($kartu);
        @endphp

        @if (isset($role))
            @role($role)
                <x-kartu-total :text="$text" :total="$total" :route="$route" :icon="$icon" :color="$color" />
            @endrole
        @else
            <x-kartu-total :text="$text" :total="$total" :route="$route" :icon="$icon" :color="$color" />
        @endif
    @endforeach
</div>