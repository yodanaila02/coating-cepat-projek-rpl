-- =====================================================================
-- SEED DATA "COATING CEPAT" — import SETELAH schema.sql
-- =====================================================================

SET NAMES utf8mb4;

-- ============================================================
-- ADMIN DEFAULT
-- Email   : admin@coatingcepat.com
-- Password: admin123   <-- (plaintext untuk login pertama, SEGERA GANTI!)
-- ============================================================
INSERT INTO `admins` (`name`, `email`, `password`) VALUES
('admin', 'admin@coatingcepat.com', '$2y$10$T2Ocyu2neZJ95ebEEBcPyOmlevoyxKQiTTPJjncwW3kx9r8.iZMku');

-- ============================================================
-- LAYANAN (5 layanan, harga realistis dalam Rupiah)
-- ============================================================
INSERT INTO `services` (`id`,`name`,`slug`,`price`,`description`,`benefits`,`duration`,`category`,`thumbnail`,`is_featured`) VALUES
(1,'Nano Ceramic Coating','nano-ceramic-coating',2500000,
'Lapisan proteksi nano ceramic 9H premium yang melindungi cat mobil Anda dari sinar UV, jamur, water spot, dan goresan halus. Memberikan efek daun talas (hydrophobic) serta kilap showroom yang tahan hingga 3-5 tahun dengan perawatan yang tepat.',
'Proteksi cat hingga 3-5 tahun\nEfek hydrophobic (air langsung mengalir)\nKilap kaca / glossy showroom\nTahan sinar UV & jamur\nMudah dicuci, kotoran tidak menempel\nGaransi aplikasi resmi',
'1-2 hari kerja','coating',NULL,1),
(2,'Paint Correction','paint-correction',1500000,
'Proses koreksi cat multi-tahap untuk menghilangkan baret halus (swirl mark), hologram, oksidasi, dan water spot membandel. Mengembalikan kedalaman warna dan kejernihan cat seperti baru keluar dari dealer.',
'Menghilangkan swirl mark & baret halus\nMenghilangkan hologram bekas poles\nMengangkat oksidasi & water spot\nWarna cat kembali dalam & tajam\nPersiapan ideal sebelum coating',
'6-10 jam','paint-correction',NULL,1),
(3,'Interior Detailing','interior-detailing',750000,
'Pembersihan menyeluruh kabin mobil: jok, plafon, karpet, dashboard, hingga celah-celah ventilasi AC. Menggunakan steam cleaner dan bahan premium yang aman untuk kulit maupun fabric, plus perlindungan UV untuk dashboard.',
'Deep cleaning jok kulit / fabric\nSteam cleaning bebas kuman & bau\nDashboard & trim terlindungi UV\nKabin wangi dan segar kembali\nAman untuk semua material interior',
'4-6 jam','interior',NULL,1),
(4,'Headlamp Restoration','headlamp-restoration',350000,
'Restorasi mika lampu utama yang sudah kuning, buram, atau berjamur. Proses wet sanding bertahap, polishing, dan dilapisi coating UV protection agar hasil bening tahan lama dan cahaya lampu kembali maksimal.',
'Mika lampu bening seperti baru\nCahaya lampu lebih terang & fokus\nDilapisi UV protection\nLebih aman berkendara malam hari\nMeningkatkan tampilan & nilai jual mobil',
'1-2 jam','headlamp',NULL,1),
(5,'Engine Detailing','engine-detailing',450000,
'Pembersihan ruang mesin secara aman dan detail menggunakan degreaser khusus dan steam. Komponen elektronik dilindungi selama proses. Diakhiri dressing agar ruang mesin tampak bersih, rapi, dan terawat.',
'Ruang mesin bersih bebas oli & debu\nProses aman untuk komponen elektronik\nMencegah karat & penumpukan kotoran\nMemudahkan deteksi kebocoran\nNilai jual kembali lebih tinggi',
'2-3 jam','engine',NULL,1);

-- ============================================================
-- GALERI (6+ foto contoh — ganti dengan foto asli via admin)
-- ============================================================
INSERT INTO `gallery` (`title`,`description`,`category`,`image`) VALUES
('Ceramic Coating Honda Civic','Hasil coating 9H, kilap maksimal','coating','sample-coating-1.jpg'),
('Coating Toyota Fortuner Hitam','Deep gloss finish + hydrophobic','coating','sample-coating-2.jpg'),
('Paint Correction Avanza Putih','Swirl mark hilang total','paint-correction','sample-pc-1.jpg'),
('Interior Detailing Innova','Kabin bersih & wangi kembali','interior','sample-interior-1.jpg'),
('Restorasi Headlamp Jazz','Mika kuning kembali bening','headlamp','sample-headlamp-1.jpg'),
('Engine Detailing Pajero','Ruang mesin bersih terawat','engine','sample-engine-1.jpg'),
('Coating Mazda CX-5','Proteksi premium 5 tahun','coating','sample-coating-3.jpg');

-- ============================================================
-- BEFORE-AFTER (3 contoh)
-- ============================================================
INSERT INTO `before_after` (`title`,`service_id`,`before_image`,`after_image`) VALUES
('Paint Correction - Kap Mesin Hitam',2,'sample-before-1.jpg','sample-after-1.jpg'),
('Headlamp Restoration - Toyota Yaris',4,'sample-before-2.jpg','sample-after-2.jpg'),
('Ceramic Coating - Bodi Samping SUV',1,'sample-before-3.jpg','sample-after-3.jpg');

-- ============================================================
-- TESTIMONI (5 contoh)
-- ============================================================
INSERT INTO `testimonials` (`name`,`vehicle`,`rating`,`comment`,`photo`) VALUES
('Budi Santoso','Toyota Fortuner 2022',5,'Hasil coatingnya luar biasa, mobil jadi kinclong banget kayak baru keluar showroom. Air langsung ngalir pas hujan. Recommended!',NULL),
('Rina Wijaya','Honda HR-V 2021',5,'Pelayanan ramah, pengerjaan rapi dan tepat waktu. Interior mobil saya wangi dan bersih total. Pasti balik lagi.',NULL),
('Andi Prasetyo','Mitsubishi Pajero 2020',5,'Paint correction-nya mantap, baret halus bekas cuci sembarangan hilang semua. Worth it dengan harganya.',NULL),
('Siti Nurhaliza','Daihatsu Terios 2019',4,'Headlamp yang tadinya kuning buram sekarang bening lagi. Lampu jadi lebih terang waktu malam. Terima kasih Coating Cepat!',NULL),
('Hendra Gunawan','Mazda CX-5 2023',5,'Booking online-nya gampang, tinggal pilih jadwal lalu konfirmasi WA. Hasil kerjanya detail dan profesional.',NULL);

-- ============================================================
-- FAQ (9 item, 3 per kategori)
-- ============================================================
INSERT INTO `faqs` (`category`,`question`,`answer`) VALUES
('Ceramic Coating','Berapa lama ketahanan ceramic coating?','Dengan perawatan yang benar (cuci rutin dengan shampo pH netral), ceramic coating kami bertahan 3-5 tahun. Kami juga menyediakan maintenance berkala untuk menjaga performa coating.'),
('Ceramic Coating','Apakah mobil baru perlu di-coating?','Justru sangat disarankan! Cat mobil baru masih dalam kondisi terbaik sehingga hasil coating akan maksimal dan proteksi dimulai sejak dini sebelum muncul baret atau jamur.'),
('Ceramic Coating','Bolehkah mobil dicuci setelah coating?','Hindari mencuci mobil selama 7 hari pertama agar coating mengeras sempurna (curing). Setelah itu mobil boleh dicuci seperti biasa, bahkan lebih mudah karena kotoran tidak menempel.'),
('Detailing','Apa bedanya detailing dengan salon mobil biasa?','Detailing dikerjakan lebih teliti per panel dengan alat ukur ketebalan cat, lighting khusus, dan produk premium. Fokusnya restorasi dan proteksi jangka panjang, bukan sekadar tampilan sesaat.'),
('Detailing','Apakah paint correction aman untuk cat mobil?','Aman. Kami selalu mengukur ketebalan cat dengan coating thickness gauge sebelum proses, sehingga pengikisan clear coat tetap dalam batas aman.'),
('Detailing','Interior kulit apakah aman di-detailing?','Sangat aman. Kami menggunakan leather cleaner dan conditioner khusus yang justru menutrisi kulit agar tidak kering dan retak.'),
('Booking','Bagaimana cara booking layanan?','Isi form di halaman Booking, pilih layanan, tanggal, dan jam yang tersedia. Setelah submit Anda akan mendapat kode booking dan tombol konfirmasi via WhatsApp.'),
('Booking','Apakah bisa reschedule jadwal booking?','Bisa. Hubungi kami via WhatsApp minimal H-1 dengan menyertakan kode booking Anda, tim kami akan membantu mengatur ulang jadwal.'),
('Booking','Bagaimana cara cek status booking saya?','Buka halaman Tracking lalu masukkan kode booking (format CC-XXXXXXXX-XXXX) yang Anda terima saat booking. Status dan jadwal akan tampil lengkap.');

-- ============================================================
-- PENGATURAN DEFAULT (FITUR: Kelola Pengaturan Website)
-- ============================================================
INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES
('site_name','Coating Cepat'),
('whatsapp','081279788675'),
('instagram','coatingcepat'),
('address','Fajar, Surakarta, Jawa Tengah'),
('maps_url','https://www.google.com/maps?q=Fajar,+Surakarta,+Jawa+Tengah&output=embed'),
('logo',''),
('open_hours','Senin - Sabtu: 08.00 - 17.00 WIB | Minggu: Tutup'),
('meta_title','Coating Cepat - Nano Ceramic Coating & Detailing Mobil Surakarta'),
('meta_description','Jasa nano ceramic coating, paint correction, dan detailing mobil premium di Surakarta. Proteksi premium, kilap showroom yang tahan lama. Booking online sekarang!'),
('daily_quota','15'),
('time_slots','09:00,10:00,11:00,13:00,14:00,15:00,16:00'),
('stat_cars','350'),
('stat_years','5'),
('stat_rating','4.9'),
('stat_customers','300');
