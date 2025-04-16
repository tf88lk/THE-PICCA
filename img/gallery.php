<?php
$folder = 'images';
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$files = array_diff(scandir($folder, SCANDIR_SORT_DESCENDING), ['.', '..']);

$images = [];

foreach ($files as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $path = "$folder/$file";
        $date = filemtime($path);
        $filesize = filesize($path);
        
        if ($filesize < 1048576) {
            $size = number_format($filesize / 1024, 0) . " KB";
        } else {
            $size = number_format($filesize / 1048576, 2) . " MB";
        }

        $images[] = [
            'name' => $file,
            'path' => $path,
            'date' => $date,
            'formattedDate' => date("Y-m-d H:i:s", $date),
            'size' => $size
        ];
    }
}

usort($images, fn($a, $b) => $b['date'] - $a['date']);
?>
<div class="gallery">
    <div class="counter">IMAGES: <span id="count"><?= count($images) ?></span></div>
	<?php if (!empty($images)): ?>
		<div id="background-image" data-src="<?= $images[0]['path'] ?>"></div>
	<?php endif; ?>
    <?php foreach ($images as $img): ?>
        <div class="image-box" data-id="<?= htmlspecialchars($img['name']) ?>">
            <a href="<?= htmlspecialchars($img['path']) ?>" target="_blank">
                <img src="<?= htmlspecialchars($img['path']) ?>" alt="obraz">
            </a>
            <div class="date">
				<?= $img['formattedDate'] ?> • 
			<span class="size"><?= $img['size'] ?></span>
			</div>
        </div>
    <?php endforeach; ?>
</div>
