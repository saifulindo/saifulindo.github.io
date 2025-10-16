<?php
// Tampilkan pesan status setelah form disubmit
$statusMsg = '';

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    
    // Validasi bahwa semua field yang diperlukan telah diisi
    if (isset($_POST['no_absen'], $_POST['nama_lengkap'], $_POST['tugas']) && !empty($_FILES["pdfFile"]["name"])) {
        
        // Ambil data dari form
        $no_absen = $_POST['no_absen'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $tugas = $_POST['tugas'];
        
        // Informasi file
        $fileName = basename($_FILES["pdfFile"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        
        // Tentukan ekstensi yang diizinkan
        $allowedTypes = array('pdf');
        
        if (in_array(strtolower($fileType), $allowedTypes)) {
            
            // --- PEMBUATAN FOLDER DAN NAMA FILE ---

            // 1. Buat nama folder berdasarkan jenis tugas (ganti spasi dengan underscore)
            $folderName = str_replace(' ', '_', $tugas);
            $targetDir = "uploads/" . $folderName . "/";
            
            // 2. Buat folder jika belum ada
            if (!file_exists($targetDir)) {
                // mkdir akan membuat direktori secara rekursif (true)
                mkdir($targetDir, 0777, true);
            }
            
            // 3. Buat nama file acak untuk menghindari duplikasi
            // uniqid() menghasilkan ID unik berdasarkan waktu
            $randomFileName = uniqid() . '.' . $fileType;
            
            // 4. Buat format nama file akhir yang akan disimpan
            // Sanitasi nama lengkap untuk keamanan
            $sanitized_nama_lengkap = preg_replace("/[^a-zA-Z0-9\s]/", "", $nama_lengkap);
            $sanitized_nama_lengkap = str_replace(' ', '_', $sanitized_nama_lengkap);
            
            $newFileName = $no_absen . "-" . $sanitized_nama_lengkap . "-" . $folderName . "-" . $randomFileName;
            $targetFilePath = $targetDir . $newFileName;
            
            // --- PROSES UPLOAD ---
            
            // Pindahkan file dari temporary location ke folder tujuan
            if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFilePath)) {
                $statusMsg = "File berhasil diunggah dengan nama: " . htmlspecialchars($newFileName);
            } else {
                $statusMsg = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            }
            
        } else {
            $statusMsg = 'Maaf, hanya file dengan format PDF yang diizinkan.';
        }
    } else {
        $statusMsg = 'Harap isi semua kolom dan pilih file untuk diunggah.';
    }
}

// Tampilkan pesan status
echo $statusMsg;

// Tambahkan link untuk kembali ke halaman utama
echo '<br><a href="index.html">Kembali ke Halaman Unggah</a>';
?>