<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'POST';
if ($method !== 'POST') {
    json_response(false, 'Method not allowed.', null, 405);
}

require_admin();

$type = trim((string)($_POST['type'] ?? $_GET['type'] ?? ''));
$map = [
    'divisi' => ['dir' => __DIR__ . '/../../asset/img', 'prefix' => 'divisi_', 'url' => '/asset/img/'],
    'event' => ['dir' => __DIR__ . '/../../asset/img', 'prefix' => 'event_', 'url' => '/asset/img/'],
    'member' => ['dir' => __DIR__ . '/../../asset/img', 'prefix' => 'member_', 'url' => '/asset/img/'],
    'proker' => ['dir' => __DIR__ . '/../../uploads/proker', 'prefix' => 'proker_', 'url' => '/uploads/proker/'],
];

if (!isset($map[$type])) {
    json_response(false, 'Invalid upload type.', null, 422);
}

if (!isset($_FILES['file'])) {
    json_response(false, 'No file uploaded.', null, 422);
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    json_response(false, 'Upload failed.', null, 400);
}

if ($file['size'] > 5 * 1024 * 1024) {
    json_response(false, 'File size exceeds 5MB.', null, 422);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
if ($finfo) {
    finfo_close($finfo);
}

$allowed = [
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/webp' => '.webp',
];

if (!$mime || !isset($allowed[$mime])) {
    json_response(false, 'Only JPG, PNG, and WEBP are allowed.', null, 422);
}

$targetDir = $map[$type]['dir'];
if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
    json_response(false, 'Failed to create upload directory.', null, 500);
}

try {
    $token = bin2hex(random_bytes(8));
} catch (Exception $e) {
    $token = str_replace('.', '', uniqid('', true));
}

$filename = $map[$type]['prefix'] . $token . $allowed[$mime];
$destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    json_response(false, 'Failed to save uploaded file.', null, 500);
}

json_response(true, 'Upload successful.', [
    'path' => $map[$type]['url'] . $filename,
]);
