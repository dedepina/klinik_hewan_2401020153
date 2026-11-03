<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

if (!function_exists('statusUsiaHewan')) {
    function statusUsiaHewan(int $usia): string
    {
        if ($usia >= 3) {
            return 'Dewasa';
        }

        return 'Muda';
    }
}

$aturanPasien = [
    'kode_pasien' => ['required', 'regex:/^[A-Za-z0-9]{10}$/'],
    'nama_hewan' => ['required', 'string', 'min:2', 'max:100'],
    'nomor_rekam' => ['required', 'string', 'min:5', 'max:30'],
    'usia_tahun' => ['required', 'integer', 'min:0', 'max:30'],
    'jenis_hewan_id' => ['required', 'integer'],
];

$pesanPasien = [
    'kode_pasien.required' => 'Kode pasien wajib diisi.',
    'kode_pasien.regex' => 'Kode pasien harus tepat 10 karakter huruf atau angka.',
    'nama_hewan.required' => 'Nama hewan wajib diisi.',
    'nama_hewan.min' => 'Nama hewan minimal 2 karakter.',
    'nama_hewan.max' => 'Nama hewan maksimal 100 karakter.',
    'nomor_rekam.required' => 'Nomor rekam wajib diisi.',
    'nomor_rekam.min' => 'Nomor rekam minimal 5 karakter.',
    'nomor_rekam.max' => 'Nomor rekam maksimal 30 karakter.',
    'usia_tahun.required' => 'Usia wajib diisi.',
    'usia_tahun.integer' => 'Usia harus berupa bilangan bulat.',
    'usia_tahun.min' => 'Usia minimal 0 tahun.',
    'usia_tahun.max' => 'Usia maksimal 30 tahun.',
    'jenis_hewan_id.required' => 'Jenis hewan wajib dipilih.',
    'jenis_hewan_id.integer' => 'Jenis hewan tidak valid.',
];

Route::get('/pasien-hewan', function (Request $request) {
    $kataKunci = strip_tags(trim((string) $request->query('q', '')));
    $jenisHewanId = $request->query('jenis_hewan_id', '');

    try {
        $pdo = DB::connection()->getPdo();

        $sql = 'SELECT p.id, p.kode_pasien, p.nama_hewan, p.nomor_rekam,
                       p.usia_tahun, p.jenis_hewan_id, j.nama_jenis
                FROM pasien_hewan AS p
                JOIN jenis_hewan AS j
                    ON j.id = p.jenis_hewan_id
                WHERE 1 = 1';
        $parameter = [];

        if ($kataKunci !== '') {
            $sql .= ' AND (p.kode_pasien LIKE :kata_kunci_kode
                      OR p.nama_hewan LIKE :kata_kunci_nama
                      OR p.nomor_rekam LIKE :kata_kunci_rekam)';
            $nilaiPencarian = '%' . $kataKunci . '%';
            $parameter['kata_kunci_kode'] = $nilaiPencarian;
            $parameter['kata_kunci_nama'] = $nilaiPencarian;
            $parameter['kata_kunci_rekam'] = $nilaiPencarian;
        }

        if ($jenisHewanId !== '') {
            $sql .= ' AND p.jenis_hewan_id = :jenis_hewan_id';
            $parameter['jenis_hewan_id'] = (int) $jenisHewanId;
        }

        $sql .= ' ORDER BY p.kode_pasien';
        $ambilPasien = $pdo->prepare($sql);
        $ambilPasien->execute($parameter);
        $daftarPasien = $ambilPasien->fetchAll(\PDO::FETCH_ASSOC);

        $ambilJenis = $pdo->prepare(
            'SELECT id, nama_jenis FROM jenis_hewan ORDER BY nama_jenis'
        );
        $ambilJenis->execute();
        $daftarJenisHewan = $ambilJenis->fetchAll(\PDO::FETCH_ASSOC);

        return view('pasien-hewan', compact(
            'daftarPasien',
            'daftarJenisHewan',
            'kataKunci',
            'jenisHewanId'
        ));
    } catch (\Throwable $error) {
        report($error);

        return response(
            'Data tidak dapat dibaca. Periksa koneksi dan query.',
            500
        );
    }
})->name('pasien.index');

Route::post('/pasien-hewan', function (Request $request) use ($aturanPasien, $pesanPasien) {
    $request->merge([
        'kode_pasien' => strip_tags(trim((string) $request->input('kode_pasien'))),
        'nama_hewan' => strip_tags(trim((string) $request->input('nama_hewan'))),
        'nomor_rekam' => strip_tags(trim((string) $request->input('nomor_rekam'))),
    ]);

    $data = $request->validate($aturanPasien, $pesanPasien);

    try {
        $pdo = DB::connection()->getPdo();

        $cekJenis = $pdo->prepare(
            'SELECT COUNT(*) FROM jenis_hewan WHERE id = :id'
        );
        $cekJenis->execute(['id' => $data['jenis_hewan_id']]);

        if ((int) $cekJenis->fetchColumn() === 0) {
            return back()->withInput()->with(
                'gagal',
                'Jenis hewan tidak tersedia.'
            );
        }

        $cekDuplikat = $pdo->prepare(
            'SELECT COUNT(*) FROM pasien_hewan
             WHERE kode_pasien = :kode_pasien OR nomor_rekam = :nomor_rekam'
        );
        $cekDuplikat->execute([
            'kode_pasien' => $data['kode_pasien'],
            'nomor_rekam' => $data['nomor_rekam'],
        ]);

        if ((int) $cekDuplikat->fetchColumn() > 0) {
            return back()->withInput()->with(
                'gagal',
                'Kode pasien atau nomor rekam sudah digunakan.'
            );
        }

        $simpan = $pdo->prepare(
            'INSERT INTO pasien_hewan
             (kode_pasien, nama_hewan, nomor_rekam, usia_tahun, jenis_hewan_id)
             VALUES (:kode_pasien, :nama_hewan, :nomor_rekam,
                     :usia_tahun, :jenis_hewan_id)'
        );
        $simpan->execute($data);

        return redirect()->route('pasien.index')->with(
            'berhasil',
            'Data pasien berhasil ditambahkan.'
        );
    } catch (\Throwable $error) {
        report($error);

        return back()->withInput()->with(
            'gagal',
            'Data gagal disimpan. Periksa koneksi dan data.'
        );
    }
})->name('pasien.store');