<?php
$folder = __DIR__ . '/images/';
$today = date('Y-m-d');
$zipName = "all-images_$today.zip";

$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!is_dir($folder)) {
    exit("Folder nie istnieje.");
}

$files = scandir($folder);
$zip = new ZipArchive();

$tmpZipPath = sys_get_temp_dir() . '/' . uniqid('galeria_', true) . '.zip';

if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Nie można utworzyć pliku ZIP.");
}

foreach ($files as $file) {
    $filePath = $folder . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_types) && is_file($filePath)) {
        $zip->addFile($filePath, $file);
    }
}

$zip->close();

if (!file_exists($tmpZipPath) || filesize($tmpZipPath) === 0) {
    exit("Błąd: plik ZIP jest pusty.");
}

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"$zipName\"");
header('Content-Length: ' . filesize($tmpZipPath));
readfile($tmpZipPath);

unlink($tmpZipPath);
exit;
?>
