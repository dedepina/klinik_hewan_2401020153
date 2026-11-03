USE klinik_hewan_2401020153;

INSERT INTO jenis_hewan (nama_jenis) VALUES
    ('Kucing'),
    ('Anjing');

INSERT INTO pasien_hewan
    (kode_pasien, nama_hewan, nomor_rekam, usia_tahun, jenis_hewan_id)
VALUES
    ('PH00000001', 'Milo', 'RMH-0001', 3, 1),
    ('PH00000002', 'Bruno', 'RMH-0002', 5, 2),
    ('PH00000003', 'Luna', 'RMH-0003', 2, 1);