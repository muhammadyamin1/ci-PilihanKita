<?php
// generate_test_csv.php - Jalankan sekali saja

$filename = 'test_import_500.csv';
$handle = fopen($filename, 'w');

// Header (gunakan semicolon ; sesuai template Anda)
fwrite($handle, "Nama;Username;Email;Password\n");

for ($i = 1; $i <= 500; $i++) {
    $nama     = "Pemilih Test " . $i;
    $username = "pemilih_test_" . str_pad($i, 4, '0', STR_PAD_LEFT);
    $email    = "pemilih" . $i . "@test.com";
    
    // Kosongkan password di 70% data agar sistem generate otomatis
    // Isi password di 30% data
    if ($i % 3 === 0) {
        $password = "Pass" . rand(1000, 9999) . "abc";
    } else {
        $password = "";   // kosong → sistem akan generate otomatis
    }

    $line = $nama . ";" . $username . ";" . $email . ";" . $password . "\n";
    fwrite($handle, $line);
}

fclose($handle);

echo "✅ File CSV berhasil dibuat: <strong>$filename</strong><br>";
echo "Jumlah data: 500 baris<br>";
echo "<a href='$filename' download>↓ Download test_import_500.csv</a>";