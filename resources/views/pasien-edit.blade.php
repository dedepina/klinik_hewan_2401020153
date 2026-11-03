<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Pasien</title>
</head>
<body>
    <h1>Ubah Pasien</h1>

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

    <form action="{{ route('pasien.update', $pasien['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <p>Kode Pasien<br>
            <input type="text" name="kode_pasien"
                value="{{ old('kode_pasien', $pasien['kode_pasien']) }}">
        </p>
        <p>Nama Hewan<br>
            <input type="text" name="nama_hewan"
                value="{{ old('nama_hewan', $pasien['nama_hewan']) }}">
        </p>
        <p>Nomor Rekam<br>
            <input type="text" name="nomor_rekam"
                value="{{ old('nomor_rekam', $pasien['nomor_rekam']) }}">
        </p>
        <p>Usia (tahun)<br>
            <input type="number" name="usia_tahun"
                value="{{ old('usia_tahun', $pasien['usia_tahun']) }}">
        </p>
        <p>Jenis Hewan<br>
            <select name="jenis_hewan_id">
                @foreach ($daftarJenisHewan as $jenis)
                    <option value="{{ $jenis['id'] }}"
                        @selected(old('jenis_hewan_id', $pasien['jenis_hewan_id']) == $jenis['id'])>
                        {{ $jenis['nama_jenis'] }}
                    </option>
                @endforeach
            </select>
        </p>
        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('pasien.index') }}">Kembali</a>
    </form>
</body>
</html>