<?php
// Tampilkan semua error untuk debugging. Hapus baris ini di lingkungan produksi.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan form telah disubmit
if (isset($_POST["submit"])) {

    // --- PENGATURAN DAN DEFINISI ---
    $font_path = __DIR__ . '/Inter-Regular.ttf';
    $font_path_bold = __DIR__ . '/Inter-Bold.ttf'; // Font untuk teks tebal
    $template_path = __DIR__ . '/card_template.jpg';

    // Periksa file yang diperlukan
    if (!file_exists($font_path)) {
        die("Error: File font 'Inter-Regular.ttf' tidak ditemukan.");
    }
    if (!file_exists($font_path_bold)) {
        die("Error: File font 'Inter-Bold.ttf' tidak ditemukan.");
    }
    if (!file_exists($template_path)) {
        die("Error: File template 'card_template.jpg' tidak ditemukan.");
    }

    // Ambil data dari form
    $university_name = $_POST['university_name'];
    $name = $_POST['name'];
    $class = $_POST['class'];
    $roll = $_POST['roll'];
    $dob = date("Y-m-d", strtotime($_POST['dob']));
    $year = $_POST['year'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];

    // Membuat gambar dari template
    $image = @imagecreatefromjpeg($template_path);
    if (!$image) {
        die("Error: Gagal memuat 'card_template.jpg'. Pastikan file tersebut adalah JPEG yang valid.");
    }

    // Definisikan warna
    $text_color_dark = imagecolorallocate($image, 0, 0, 0);
    $text_color_white = imagecolorallocate($image, 255, 255, 255);

    // --- PEMROSESAN GAMBAR YANG DIUNGGAH ---

    // Proses Logo Universitas
    if (isset($_FILES['university_logo']) && $_FILES['university_logo']['error'] == 0) {
        $logo_tmp_path = $_FILES['university_logo']['tmp_name'];
        $logo_image = @imagecreatefromstring(file_get_contents($logo_tmp_path));
        if ($logo_image) {
            imagecopyresampled($image, $logo_image, 325, 199, 0, 0, 162, 194, imagesx($logo_image), imagesy($logo_image));
            imagedestroy($logo_image);
        } else {
            error_log("Gagal memproses file logo.");
        }
    }

    // Proses Foto Mahasiswa
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] == 0) {
        $photo_tmp_path = $_FILES['student_photo']['tmp_name'];
        $photo_image = @imagecreatefromstring(file_get_contents($photo_tmp_path));
        if ($photo_image) {
            imagecopyresampled($image, $photo_image, 259, 527, 0, 0, 282, 318, imagesx($photo_image), imagesy($photo_image));
            imagedestroy($photo_image);
        } else {
            error_log("Gagal memproses file foto mahasiswa.");
        }
    }

    // --- PENULISAN TEKS PADA GAMBAR ---
    
    // Menambahkan Nama Universitas
    $univ_font_size = 25; 
    imagettftext($image, $univ_font_size, 0, 72, 495, $text_color_dark, $font_path_bold, $university_name);
    
    // Menambahkan detail mahasiswa
    $details_font_size = 20; 
    $details_margin_x = 200;
    
    imagettftext($image, $details_font_size, 0, $details_margin_x, 905, $text_color_dark, $font_path, $name);
    imagettftext($image, $details_font_size, 0, $details_margin_x, 945, $text_color_dark, $font_path, $class);
    imagettftext($image, $details_font_size, 0, $details_margin_x, 985, $text_color_dark, $font_path, $roll);
    imagettftext($image, $details_font_size, 0, 265, 1030, $text_color_dark, $font_path, $dob);
    imagettftext($image, $details_font_size, 0, $details_margin_x, 1070, $text_color_dark, $font_path, $year);

    // Menambahkan detail di footer
    $footer_font_size = 20; 
    $footer_margin_x = 215;

    imagettftext($image, $footer_font_size, 0, $footer_margin_x, 1163, $text_color_white, $font_path, $address);
    imagettftext($image, $footer_font_size, 0, $footer_margin_x, 1207, $text_color_white, $font_path, $mobile);


    // --- KELUARKAN GAMBAR LANGSUNG KE BROWSER ---
    
    // Atur header untuk memberitahu browser bahwa ini adalah gambar JPEG
    header('Content-Type: image/jpeg');
    // Sarankan nama file untuk diunduh
    header('Content-Disposition: inline; filename="student_id_card.jpg"');

    // Keluarkan data gambar
    imagejpeg($image);

    // Bersihkan memori
    imagedestroy($image);

    exit();

} else {
    // Jika formulir tidak disubmit, kembali ke halaman utama.
    header('Location: index.html');
    exit();
}
?>
