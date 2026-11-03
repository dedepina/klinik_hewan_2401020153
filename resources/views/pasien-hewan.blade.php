<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasien Klinik Hewan</title>
</head>
<body>
    <h1>Pasien Klinik Hewan</h1>

    @if (session('berhasil'))
        <p>{{ session('berhasil') }}</p>
    @endif
    @if (session('gagal'))
        <p>{{ session('gagal') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Tambah Pasien</h2>
    <form action="{{ route('pasien.store') }}" method="POST">
        @csrf
        <p>Kode Pasien<br>
            <input type="text" name="kode_pasien" value="{{ old('kode_pasien') }}">
        </p>
        <p>Nama Hewan<br>
            <input type="text" name="nama_hewan" value="{{ old('nama_hewan') }}">
        </p>
        <p>Nomor Rekam<br>
            <input type="text" name="nomor_rekam" value="{{ old('nomor_rekam') }}">
        </p>
        <p>Usia (tahun)<br>
            <input type="number" name="usia_tahun" value="{{ old('usia_tahun') }}">
        </p>
        <p>Jenis Hewan<br>
            <select name="jenis_hewan_id">
                <option value="">-- Pilih Jenis Hewan --</option>
                @foreach ($daftarJenisHewan as $jenis)
                    <option value="{{ $jenis['id'] }}"
                        @selected(old('jenis_hewan_id') == $jenis['id'])>
                        {{ $jenis['nama_jenis'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Tambah Data</button>
    </form>

    <h2>Pencarian dan Filter</h2>
    <form action="{{ route('pasien.index') }}" method="GET">
        <p>Kata Kunci (kode, nama hewan, nomor rekam)<br>
            <input type="text" name="q" value="{{ $kataKunci }}">
        </p>
        <p>Jenis Hewan<br>
            <select name="jenis_hewan_id">
                <option value="">-- Semua Jenis Hewan --</option>
                @foreach ($daftarJenisHewan as $jenis)
                    <option value="{{ $jenis['id'] }}"
                        @selected((string) $jenisHewanId === (string) $jenis['id'])>
                        {{ $jenis['nama_jenis'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Cari</button>
        <a href="{{ route('pasien.index') }}">Reset</a>
    </form>

    <h2>Daftar Pasien</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>Kode</th><th>Nama Hewan</th><th>No. Rekam</th>
            <th>Usia (tahun)</th><th>Status Usia</th>
            <th>Jenis Hewan</th><th>Aksi</th>
        </tr>
        @forelse ($daftarPasien as $pasien)
            <tr>
                <td>{{ $pasien['kode_pasien'] }}</td>
                <td>{{ $pasien['nama_hewan'] }}</td>
                <td>{{ $pasien['nomor_rekam'] }}</td>
                <td>{{ $pasien['usia_tahun'] }}</td>
                <td>{{ statusUsiaHewan((int) $pasien['usia_tahun']) }}</td>
                <td>{{ $pasien['nama_jenis'] }}</td>
                <td>
                    <a href="{{ route('pasien.edit', $pasien['id']) }}">Ubah</a>
                    <form action="{{ route('pasien.destroy', $pasien['id']) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">Data tidak ditemukan.</td></tr>
        @endforelse
    </table>
</body>
</html>