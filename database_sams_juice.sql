-- ============================================================
--  SAM'S JUICE - Oracle Database Setup Script
--  Database  : Oracle Free (FREEPDB1)
--  Schema/User: SAMSJUICE
--  Generated  : 2026-06-11
--  Description: Full schema + seed data dari analisis kode PHP
-- ============================================================
--
-- CARA PAKAI DI VPS:
--   1. Login sebagai SYSDBA:
--        sqlplus sys/your_sys_pass@localhost/FREEPDB1 as sysdba
--   2. Jalankan file ini:
--        @/path/to/database_sams_juice.sql
--      atau paste isi file ini ke SQL*Plus / SQL Developer.
--
-- ============================================================


-- ============================================================
-- BAGIAN 1: BUAT USER / SCHEMA
-- ============================================================

-- Buat user (jalankan sebagai SYSDBA di container FREEPDB1)
CREATE USER samsjuice
    IDENTIFIED BY sams123
    DEFAULT TABLESPACE USERS
    TEMPORARY TABLESPACE TEMP
    QUOTA UNLIMITED ON USERS;

-- Berikan privilege minimum yang dibutuhkan
GRANT CONNECT, RESOURCE, CREATE SESSION TO samsjuice;
GRANT CREATE TABLE, CREATE SEQUENCE, CREATE VIEW TO samsjuice;

-- Koneksi di koneksi.php menggunakan user ini:
--   oci_connect("samsjuice", "sams123", "localhost/FREEPDB1")


-- ============================================================
-- BAGIAN 2: PINDAH KE SCHEMA SAMSJUICE
-- (Jalankan semua DDL berikut sebagai user samsjuice,
--  ATAU tambahkan prefix samsjuice. pada nama objek jika
--  tetap login sebagai SYSDBA)
-- ============================================================
-- Untuk berpindah user di SQL*Plus:
--   CONNECT samsjuice/sams123@localhost/FREEPDB1


-- ============================================================
-- TABEL 1: USERS
-- Menyimpan data login kasir dan customer.
-- Digunakan oleh: proses_login.php, proses_register.php,
--                 update_profil_kasir.php, update_customer_profile.php,
--                 profil_kasir.php, customer.php
--
-- Kolom yang dikonfirmasi dari kode PHP:
--   ID_USER   - NUMBER, primary key (pakai sequence user_seq)
--   USERNAME  - VARCHAR2, unique, dipakai sebagai identifier login
--   PASSWORD  - VARCHAR2, plain-text (sesuai kode asli)
--   ROLE      - VARCHAR2 -> nilai: 'kasir' | 'customer'
--   FOTO      - VARCHAR2, nama file foto (upload ke folder /uploads/)
--               (NULL = pakai default-profile.png)
--
-- Kolom tambahan untuk customer profile (dari customer.php):
--   EMAIL     - VARCHAR2, opsional
--   NO_HP     - VARCHAR2, opsional
--   ALAMAT    - CLOB, opsional
-- ============================================================

CREATE TABLE users (
    id_user   NUMBER        NOT NULL,
    username  VARCHAR2(100) NOT NULL,
    password  VARCHAR2(255) NOT NULL,
    role      VARCHAR2(20)  NOT NULL,
    foto      VARCHAR2(255) DEFAULT NULL,
    email     VARCHAR2(150) DEFAULT NULL,
    no_hp     VARCHAR2(20)  DEFAULT NULL,
    alamat    CLOB          DEFAULT NULL,
    CONSTRAINT pk_users      PRIMARY KEY (id_user),
    CONSTRAINT uq_username   UNIQUE (username),
    CONSTRAINT ck_role       CHECK (role IN ('kasir','customer'))
);

-- Sequence untuk ID_USER
CREATE SEQUENCE user_seq
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

COMMENT ON TABLE  users             IS 'Tabel pengguna sistem: kasir dan customer';
COMMENT ON COLUMN users.id_user     IS 'Primary key, auto-increment via user_seq';
COMMENT ON COLUMN users.username    IS 'Username unik untuk login';
COMMENT ON COLUMN users.password    IS 'Password (plain-text sesuai kode asli)';
COMMENT ON COLUMN users.role        IS 'Peran: kasir atau customer';
COMMENT ON COLUMN users.foto        IS 'Nama file foto profil di folder /uploads/';
COMMENT ON COLUMN users.email       IS 'Email customer (opsional)';
COMMENT ON COLUMN users.no_hp       IS 'Nomor HP customer (opsional)';
COMMENT ON COLUMN users.alamat      IS 'Alamat customer (opsional)';


-- ============================================================
-- TABEL 2: KATEGORI
-- Tabel referensi kategori produk.
-- Digunakan oleh: INSERT PRODUK di data_menu.php
--   (kode memakai hardcode ID_KATEGORI = 1 saat insert produk)
-- ============================================================

CREATE TABLE kategori (
    id_kategori   NUMBER        NOT NULL,
    nama_kategori VARCHAR2(100) NOT NULL,
    deskripsi     VARCHAR2(255) DEFAULT NULL,
    CONSTRAINT pk_kategori PRIMARY KEY (id_kategori)
);

COMMENT ON TABLE  kategori              IS 'Kategori produk jus';
COMMENT ON COLUMN kategori.id_kategori  IS 'Primary key kategori';
COMMENT ON COLUMN kategori.nama_kategori IS 'Nama kategori, contoh: Jus Buah, Minuman Segar';


-- ============================================================
-- TABEL 3: PRODUK
-- Menyimpan data menu / produk jus.
-- Digunakan oleh: data_menu.php, transaksi.php, customer.php,
--                 promo.php, proses_order_customer.php,
--                 kasir.php, setup_promo_db.php
--
-- Kolom yang dikonfirmasi dari kode PHP:
--   ID_PRODUK     - VARCHAR2 (format: MNU-XXXX), primary key
--   NAMA_PRODUK   - VARCHAR2
--   HARGA         - NUMBER, harga regular dalam rupiah
--   STOK          - NUMBER, jumlah stok (dikurangi saat order)
--   GAMBAR        - VARCHAR2, nama file gambar di folder /image/
--   ID_KATEGORI   - NUMBER, FK ke KATEGORI (default = 1)
--   DESKRIPSI     - CLOB, deskripsi produk
--   IS_PROMO      - NUMBER(1), flag promo: 0=tidak, 1=aktif
--                   (ditambahkan via setup_promo_db.php)
--   DISKON_PERSEN - NUMBER, persentase diskon (0-100)
--                   (ditambahkan via setup_promo_db.php)
-- ============================================================

CREATE TABLE produk (
    id_produk     VARCHAR2(20)  NOT NULL,
    nama_produk   VARCHAR2(150) NOT NULL,
    harga         NUMBER(12,0)  NOT NULL,
    stok          NUMBER(6,0)   DEFAULT 10 NOT NULL,
    gambar        VARCHAR2(255) DEFAULT 'default.jpg',
    id_kategori   NUMBER        DEFAULT 1  NOT NULL,
    deskripsi     CLOB          DEFAULT NULL,
    is_promo      NUMBER(1)     DEFAULT 0  NOT NULL,
    diskon_persen NUMBER(5,2)   DEFAULT 0  NOT NULL,
    CONSTRAINT pk_produk       PRIMARY KEY (id_produk),
    CONSTRAINT fk_produk_kat   FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
    CONSTRAINT ck_is_promo     CHECK (is_promo IN (0, 1)),
    CONSTRAINT ck_diskon       CHECK (diskon_persen BETWEEN 0 AND 100),
    CONSTRAINT ck_harga        CHECK (harga >= 0),
    CONSTRAINT ck_stok         CHECK (stok >= 0)
);

COMMENT ON TABLE  produk              IS 'Tabel menu/produk jus yang dijual';
COMMENT ON COLUMN produk.id_produk    IS 'Primary key, format MNU-XXXX (manual input)';
COMMENT ON COLUMN produk.nama_produk  IS 'Nama produk/menu';
COMMENT ON COLUMN produk.harga        IS 'Harga regular dalam rupiah';
COMMENT ON COLUMN produk.stok         IS 'Jumlah stok tersedia';
COMMENT ON COLUMN produk.gambar       IS 'Nama file gambar di folder /image/';
COMMENT ON COLUMN produk.id_kategori  IS 'FK ke tabel KATEGORI';
COMMENT ON COLUMN produk.deskripsi    IS 'Deskripsi singkat produk';
COMMENT ON COLUMN produk.is_promo     IS '0=tidak promo, 1=sedang promo';
COMMENT ON COLUMN produk.diskon_persen IS 'Persentase diskon (0-100)';


-- ============================================================
-- TABEL 4: TRANSAKSI
-- Menyimpan data transaksi penjualan (kasir & customer).
-- Digunakan oleh: simpan_transaksi.php, proses_order_customer.php,
--                 api_riwayat.php, update_status.php, cetak_invoice.php,
--                 riwayat.php, kasir.php, cetak_laporan.php, customer.php
--
-- Kolom yang dikonfirmasi dari kode PHP:
--   ID_TRANSAKSI - VARCHAR2 (format: TRX-YYYYMMDD-NNNN), primary key
--   TANGGAL      - DATE, waktu transaksi
--   PELANGGAN    - VARCHAR2, nama pelanggan / username customer
--   MENU         - VARCHAR2, ringkasan menu yang dipesan
--                  (contoh: "Jus Alpukat (2x), Jus Jeruk (1x)")
--   TOTAL        - NUMBER, total pembayaran dalam rupiah
--   METODE       - VARCHAR2, metode bayar: Tunai | QRIS | Transfer
--   STATUS       - VARCHAR2: Menunggu | Diproses | Selesai | Batal
--   NO_HP        - VARCHAR2, nomor HP pembeli (dari order customer)
--   ALAMAT       - CLOB, alamat pengiriman (dari order customer)
--
-- CATATAN INKONSISTENSI YANG DIPERBAIKI:
--   - simpan_transaksi.php (kasir) INSERT tanpa NO_HP & ALAMAT
--   - proses_order_customer.php (customer) INSERT dengan NO_HP & ALAMAT
--   -> Solusi: Kolom NO_HP & ALAMAT dibuat NULLABLE agar keduanya kompatibel
-- ============================================================

CREATE TABLE transaksi (
    id_transaksi VARCHAR2(30)  NOT NULL,
    tanggal      DATE          DEFAULT SYSDATE NOT NULL,
    pelanggan    VARCHAR2(150) DEFAULT 'Walk-in Customer' NOT NULL,
    menu         VARCHAR2(4000) NOT NULL,
    total        NUMBER(14,0)  DEFAULT 0 NOT NULL,
    metode       VARCHAR2(20)  DEFAULT 'Tunai' NOT NULL,
    status       VARCHAR2(20)  DEFAULT 'Menunggu' NOT NULL,
    no_hp        VARCHAR2(20)  DEFAULT NULL,
    alamat       CLOB          DEFAULT NULL,
    CONSTRAINT pk_transaksi    PRIMARY KEY (id_transaksi),
    CONSTRAINT ck_metode       CHECK (metode IN ('Tunai','QRIS','Transfer')),
    CONSTRAINT ck_status_trx   CHECK (status IN ('Menunggu','Diproses','Selesai','Batal')),
    CONSTRAINT ck_total        CHECK (total >= 0)
);

COMMENT ON TABLE  transaksi              IS 'Tabel transaksi penjualan (kasir & customer)';
COMMENT ON COLUMN transaksi.id_transaksi IS 'Primary key, format TRX-YYYYMMDD-NNNN';
COMMENT ON COLUMN transaksi.tanggal      IS 'Tanggal & waktu transaksi';
COMMENT ON COLUMN transaksi.pelanggan    IS 'Nama pelanggan atau username customer';
COMMENT ON COLUMN transaksi.menu         IS 'Ringkasan menu dipesan, contoh: Jus Alpukat (2x)';
COMMENT ON COLUMN transaksi.total        IS 'Total pembayaran dalam rupiah';
COMMENT ON COLUMN transaksi.metode       IS 'Metode bayar: Tunai | QRIS | Transfer';
COMMENT ON COLUMN transaksi.status       IS 'Status pesanan: Menunggu | Diproses | Selesai | Batal';
COMMENT ON COLUMN transaksi.no_hp        IS 'Nomor HP pembeli (dari order customer, NULL untuk kasir)';
COMMENT ON COLUMN transaksi.alamat       IS 'Alamat pengiriman (dari order customer, NULL untuk kasir)';


-- ============================================================
-- TABEL 5: ULASAN
-- Menyimpan ulasan/review dari pelanggan.
-- Digunakan oleh: ulasan.php, proses_ulasan.php
--
-- Kolom yang dikonfirmasi dari kode PHP:
--   ID_ULASAN  - NUMBER, primary key (kode pakai rand())
--   NAMA       - VARCHAR2, nama pengulas
--   KOMENTAR   - CLOB, isi ulasan
--   RATING     - NUMBER(1), nilai 1-5
-- ============================================================

CREATE TABLE ulasan (
    id_ulasan  NUMBER         NOT NULL,
    nama       VARCHAR2(150)  NOT NULL,
    komentar   CLOB           DEFAULT NULL,
    rating     NUMBER(1)      DEFAULT 5 NOT NULL,
    tgl_ulasan DATE           DEFAULT SYSDATE,
    CONSTRAINT pk_ulasan      PRIMARY KEY (id_ulasan),
    CONSTRAINT ck_rating      CHECK (rating BETWEEN 1 AND 5)
);

-- Sequence untuk ID_ULASAN (menggantikan rand() yang rawan duplikat)
CREATE SEQUENCE ulasan_seq
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

COMMENT ON TABLE  ulasan            IS 'Tabel ulasan/review pelanggan';
COMMENT ON COLUMN ulasan.id_ulasan  IS 'Primary key, sebaiknya pakai ulasan_seq.NEXTVAL';
COMMENT ON COLUMN ulasan.nama       IS 'Nama pengulas';
COMMENT ON COLUMN ulasan.komentar   IS 'Isi ulasan/komentar';
COMMENT ON COLUMN ulasan.rating     IS 'Rating 1-5 bintang';
COMMENT ON COLUMN ulasan.tgl_ulasan IS 'Tanggal ulasan dikirim';


-- ============================================================
-- INDEX untuk performa query
-- ============================================================

-- Index pencarian transaksi berdasarkan tanggal (dipakai di laporan)
CREATE INDEX idx_transaksi_tanggal  ON transaksi(tanggal);

-- Index pencarian transaksi berdasarkan status (dashboard kasir)
CREATE INDEX idx_transaksi_status   ON transaksi(status);

-- Index pencarian transaksi berdasarkan pelanggan (customer.php)
CREATE INDEX idx_transaksi_pelanggan ON transaksi(pelanggan);

-- Index pencarian produk berdasarkan promo
CREATE INDEX idx_produk_is_promo    ON produk(is_promo);


-- ============================================================
-- BAGIAN 3: DATA AWAL (SEED DATA)
-- ============================================================

-- ------------------------------------------------------------
-- 3A. KATEGORI
-- ------------------------------------------------------------
INSERT INTO kategori (id_kategori, nama_kategori, deskripsi)
VALUES (1, 'Jus Buah', 'Aneka jus dari buah-buahan segar');

INSERT INTO kategori (id_kategori, nama_kategori, deskripsi)
VALUES (2, 'Minuman Segar', 'Minuman dingin dan segar lainnya');

INSERT INTO kategori (id_kategori, nama_kategori, deskripsi)
VALUES (3, 'Jus Sayuran', 'Jus dari campuran sayuran sehat');


-- ------------------------------------------------------------
-- 3B. USERS ADMIN / KASIR
-- Akun admin/kasir utama
-- Username : admin
-- Password : admin123
-- Role     : kasir
-- ------------------------------------------------------------
INSERT INTO users (id_user, username, password, role, foto, email, no_hp, alamat)
VALUES (
    user_seq.NEXTVAL,
    'admin',
    'admin123',
    'kasir',
    NULL,
    'admin@samsjuice.com',
    '08123456789',
    'Jl. Contoh No. 123, Medan'
);

-- Akun kasir cadangan
-- Username : kasir1
-- Password : kasir123
INSERT INTO users (id_user, username, password, role, foto, email, no_hp, alamat)
VALUES (
    user_seq.NEXTVAL,
    'kasir',
    'kasir123',
    'kasir',
    'kasir.jpeg',
    'kasir1@samsjuice.com',
    '08234567890',
    'Jl. Setia Budi No. 45, Medan'
);

INSERT INTO users (id_user, username, password, role, foto, email, no_hp, alamat)
VALUES (
    user_seq.NEXTVAL,
    'kasir1',
    'kasir123',
    'kasir',
    'kasir1.jpeg',
    'kasir1@samsjuice.com',
    '08234567890',
    'Jl. Setia Budi No. 45, Medan'
);

-- Akun customer demo
-- Username : customer1
-- Password : cust123
INSERT INTO users (id_user, username, password, role, foto, email, no_hp, alamat)
VALUES (
    user_seq.NEXTVAL,
    'customer1',
    'cust123',
    'customer',
    NULL,
    'customer1@gmail.com',
    '08111222333',
    'Jl. Merdeka No. 10, Medan'
);


-- ------------------------------------------------------------
-- 3C. PRODUK (MENU JUS)
-- Gambar mengacu ke file yang ada di folder /image/
-- File gambar yang tersedia:
--   alpukat.jpg, jeruk.jpg, mangga.jpg, jus1.jpeg, jus4.jpg,
--   jus5.jpg, jus6.png, pokat.jpeg, banner-jus.png, botol.png
-- ------------------------------------------------------------

-- Jus Alpukat
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0001',
    'Jus Alpukat',
    20000,
    50,
    'alpukat.jpg',
    1,
    'Jus alpukat segar dengan susu dan madu, kaya lemak sehat dan vitamin E.',
    0,
    0
);

-- Jus Jeruk
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0002',
    'Jus Jeruk',
    15000,
    60,
    'jeruk.jpg',
    1,
    'Jus jeruk peras segar, kaya vitamin C dan menyegarkan.',
    0,
    0
);

-- Jus Mangga
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0003',
    'Jus Mangga',
    18000,
    45,
    'mangga.jpg',
    1,
    'Jus mangga harum manis, menggunakan mangga pilihan berkualitas tinggi.',
    0,
    0
);

-- Jus Campuran Segar
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0004',
    'Jus Mix Tropis',
    22000,
    40,
    'jus4.jpg',
    1,
    'Perpaduan jus tropis: mangga, nanas, dan jeruk dalam satu kesegaran.',
    1,
    10
);

-- Jus Stroberi
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0005',
    'Jus Stroberi',
    20000,
    35,
    'jus5.jpg',
    1,
    'Jus stroberi segar dengan rasa asam manis yang khas dan warna merah menggoda.',
    0,
    0
);

-- Jus Semangka
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0006',
    'Jus Semangka',
    15000,
    55,
    'jus6.png',
    1,
    'Jus semangka segar tanpa tambahan gula, cocok untuk diet dan hidrasi tubuh.',
    0,
    0
);

-- Jus Alpukat Kocok
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0007',
    'Jus Alpukat Kocok',
    25000,
    30,
    'pokat.jpeg',
    1,
    'Alpukat kocok ala kafe dengan susu full cream, cokelat, dan es batu serut.',
    1,
    15
);

-- Minuman Spesial Sam
INSERT INTO produk (id_produk, nama_produk, harga, stok, gambar, id_kategori, deskripsi, is_promo, diskon_persen)
VALUES (
    'MNU-0008',
    'Minuman Spesial Sam',
    28000,
    25,
    'jus1.jpeg',
    2,
    'Minuman spesial signature Sam''s Juice, campuran rahasia buah pilihan dengan sentuhan madu asli.',
    0,
    0
);


-- ------------------------------------------------------------
-- 3D. DATA ULASAN CONTOH
-- ------------------------------------------------------------
INSERT INTO ulasan (id_ulasan, nama, komentar, rating, tgl_ulasan)
VALUES (
    ulasan_seq.NEXTVAL,
    'Rina Sartika',
    'Jus alpukatnya enak banget! Segar dan harganya terjangkau. Pasti balik lagi!',
    5,
    TO_DATE('2026-05-10','YYYY-MM-DD')
);

INSERT INTO ulasan (id_ulasan, nama, komentar, rating, tgl_ulasan)
VALUES (
    ulasan_seq.NEXTVAL,
    'Budi Santoso',
    'Pelayanannya cepat, jus jeruknya mantap. Recommend buat yang suka minuman sehat.',
    5,
    TO_DATE('2026-05-15','YYYY-MM-DD')
);

INSERT INTO ulasan (id_ulasan, nama, komentar, rating, tgl_ulasan)
VALUES (
    ulasan_seq.NEXTVAL,
    'Dewi Lestari',
    'Jus mangganya juara! Manis alami tanpa rasa artifisial. Tempat ini selalu jadi pilihan.',
    4,
    TO_DATE('2026-05-20','YYYY-MM-DD')
);

INSERT INTO ulasan (id_ulasan, nama, komentar, rating, tgl_ulasan)
VALUES (
    ulasan_seq.NEXTVAL,
    'Ahmad Fauzi',
    'Harga sesuai kualitas. Jus semangkanya pas banget diminum siang hari yang panas.',
    4,
    TO_DATE('2026-06-01','YYYY-MM-DD')
);

INSERT INTO ulasan (id_ulasan, nama, komentar, rating, tgl_ulasan)
VALUES (
    ulasan_seq.NEXTVAL,
    'Siti Rahayu',
    'Minuman spesial Sam-nya enak sekali! Wajib dicoba. Porsinya juga pas dan tidak terlalu manis.',
    5,
    TO_DATE('2026-06-05','YYYY-MM-DD')
);


-- ------------------------------------------------------------
-- 3E. DATA TRANSAKSI CONTOH (sample data untuk testing)
-- ------------------------------------------------------------
INSERT INTO transaksi (id_transaksi, tanggal, pelanggan, menu, total, metode, status)
VALUES (
    'TRX-20260601-0001',
    TO_DATE('2026-06-01','YYYY-MM-DD'),
    'Walk-in Customer',
    'Jus Alpukat (2x), Jus Jeruk (1x)',
    55000,
    'Tunai',
    'Selesai'
);

INSERT INTO transaksi (id_transaksi, tanggal, pelanggan, menu, total, metode, status)
VALUES (
    'TRX-20260602-0001',
    TO_DATE('2026-06-02','YYYY-MM-DD'),
    'customer1',
    'Jus Mangga (1x)',
    18000,
    'QRIS',
    'Selesai'
);

INSERT INTO transaksi (id_transaksi, tanggal, pelanggan, menu, total, metode, status)
VALUES (
    'TRX-20260605-0001',
    TO_DATE('2026-06-05','YYYY-MM-DD'),
    'Walk-in Customer',
    'Jus Mix Tropis (2x), Jus Stroberi (1x)',
    64000,
    'Transfer',
    'Selesai'
);

INSERT INTO transaksi (id_transaksi, tanggal, pelanggan, menu, total, metode, status)
VALUES (
    'TRX-20260610-0001',
    TO_DATE('2026-06-10','YYYY-MM-DD'),
    'customer1',
    'Minuman Spesial Sam (1x)',
    28000,
    'QRIS',
    'Menunggu'
);


-- ============================================================
-- BAGIAN 4: COMMIT SEMUA PERUBAHAN
-- ============================================================
COMMIT;


-- ============================================================
-- BAGIAN 5: VERIFIKASI
-- Jalankan query berikut untuk memastikan semua tabel & data
-- sudah terbuat dengan benar.
-- ============================================================

-- Cek daftar tabel
SELECT table_name FROM user_tables ORDER BY table_name;

-- Cek jumlah data
SELECT 'USERS'     AS tabel, COUNT(*) AS jumlah FROM users
UNION ALL
SELECT 'KATEGORI', COUNT(*) FROM kategori
UNION ALL
SELECT 'PRODUK',   COUNT(*) FROM produk
UNION ALL
SELECT 'TRANSAKSI',COUNT(*) FROM transaksi
UNION ALL
SELECT 'ULASAN',   COUNT(*) FROM ulasan;

-- Cek akun kasir
SELECT id_user, username, role FROM users WHERE role = 'kasir';

-- Cek produk promo aktif
SELECT id_produk, nama_produk, harga, diskon_persen
FROM produk WHERE is_promo = 1;


-- ============================================================
-- CATATAN INKONSISTENSI YANG DITEMUKAN & TELAH DIPERBAIKI
-- ============================================================
--
-- 1. KOLOM NO_HP & ALAMAT di TRANSAKSI:
--    - simpan_transaksi.php (kasir) INSERT tanpa NO_HP & ALAMAT
--    - proses_order_customer.php INSERT dengan NO_HP & ALAMAT
--    -> SOLUSI: Kedua kolom dibuat NULLABLE (DEFAULT NULL)
--       sehingga kedua cara insert tetap bekerja.
--
-- 2. ID_ULASAN di proses_ulasan.php memakai rand(100,99999):
--    -> SOLUSI: Tetap kompatibel (rand() masih bisa dipakai),
--       namun tersedia juga ulasan_seq.NEXTVAL yang lebih aman.
--       Disarankan update proses_ulasan.php untuk pakai sequence.
--
-- 3. Kolom IS_PROMO & DISKON_PERSEN awalnya tidak ada di PRODUK
--    lalu ditambahkan via setup_promo_db.php (ALTER TABLE ADD):
--    -> SOLUSI: Kedua kolom langsung disertakan dalam CREATE TABLE
--       sehingga tidak perlu menjalankan setup_promo_db.php.
--
-- 4. Tabel KATEGORI ada FK di PRODUK (id_kategori) tapi tidak
--    ada file PHP yang mengelola KATEGORI secara langsung.
--    -> SOLUSI: Tabel dibuat dan diisi data awal. Kode PHP
--       cukup pakai id_kategori = 1 (default) seperti aslinya.
--
-- 5. Kolom EMAIL, NO_HP, ALAMAT di USERS ada di customer.php
--    (form profil customer), tapi tidak ada di proses_register.php:
--    -> SOLUSI: Kolom dibuat NULLABLE sehingga register tetap
--       bekerja tanpa mengisi kolom tersebut.
--
-- ============================================================
-- REKOMENDASI PERBAIKAN KODE PHP (opsional):
-- ============================================================
--
-- a) Gunakan PASSWORD_HASH di PHP untuk keamanan password.
-- b) Ganti rand() di proses_ulasan.php dengan ulasan_seq.NEXTVAL:
--      $query = "INSERT INTO ULASAN (ID_ULASAN, NAMA, KOMENTAR, RATING)
--                VALUES (ulasan_seq.NEXTVAL, :nama, :komentar, :rating)";
-- c) Tambahkan validasi stok sebelum order di proses_order_customer.php.
-- d) Tambahkan CATATAN / DISKON ke tabel TRANSAKSI bila diperlukan
--    (saat ini kolom DISKON tidak tersimpan di DB, hanya dihitung di JS).
--
-- ============================================================
