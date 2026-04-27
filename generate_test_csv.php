<?php
// generate_test_csv.php - Jalankan sekali saja

$filename = 'test_import_100.csv';
$handle = fopen($filename, 'w');

// Header baru sesuai template (dengan kolom No di depan)
fwrite($handle, "No;Nama;Username;Email;Password\n");

for ($i = 1; $i <= 100; $i++) {
    $no       = $i;
    $nama     = "Pemilih Test " . $i;
    $username = "pemilih_test_" . str_pad($i, 4, '0', STR_PAD_LEFT);
    $email    = "pemilih" . $i . "@test.com";
    
    // Kosongkan password di sekitar 70% data agar sistem generate otomatis
    // Isi password di 30% data
    if ($i % 3 === 0) {
        $password = "Pass" . rand(1000, 9999) . "abc";
    } else {
        $password = "";   // kosong → sistem akan generate otomatis
    }

    // Format dengan semicolon ;
    $line = $no . ";" . 
            $nama . ";" . 
            $username . ";" . 
            $email . ";" . 
            $password . "\n";
    
    fwrite($handle, $line);
}

fclose($handle);

echo "✅ File CSV berhasil dibuat: <strong>$filename</strong><br>";
echo "Jumlah data: 100 baris<br>";
echo "<a href='$filename' download>↓ Download test_import_100.csv</a>";