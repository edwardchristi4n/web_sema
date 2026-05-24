<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $id = int_or_null($_GET['id'] ?? null);
    $divisiId = int_or_null($_GET['divisi_id'] ?? null);

    if ($id) {
        $stmt = mysqli_prepare($conn, 'SELECT p.*, d.nama_divisi FROM proker p LEFT JOIN divisi d ON d.id_divisi = p.id_divisi WHERE p.id_proker = ?');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$row) {
            json_response(false, 'Proker not found.', null, 404);
        }

        json_response(true, 'Proker fetched.', $row);
    }

    if ($divisiId) {
        $stmt = mysqli_prepare($conn, 'SELECT p.*, d.nama_divisi FROM proker p LEFT JOIN divisi d ON d.id_divisi = p.id_divisi WHERE p.id_divisi = ? ORDER BY p.id_proker ASC');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $divisiId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);

        json_response(true, 'Proker list fetched.', $rows);
    }

    $result = mysqli_query($conn, 'SELECT p.*, d.nama_divisi FROM proker p LEFT JOIN divisi d ON d.id_divisi = p.id_divisi ORDER BY d.nama_divisi, p.nama_proker ASC');
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    if ($result) {
        mysqli_free_result($result);
    }

    json_response(true, 'Proker list fetched.', $rows);
}

if ($method === 'POST') {
    require_admin();
    $data = read_json_body();
    require_fields($data, ['nama_proker', 'id_divisi']);

    $nama = trim((string)$data['nama_proker']);
    $deskripsi = trim((string)($data['deskripsi'] ?? ''));
    $divisiId = int_or_null($data['id_divisi']);
    $gambar = trim((string)($data['gambar'] ?? ''));
    $status = trim((string)($data['status'] ?? ''));
    $tanggal = trim((string)($data['tanggal_pelaksanaan'] ?? ''));

    if (!$divisiId) {
        json_response(false, 'Divisi id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO proker (id_divisi, nama_proker, deskripsi, gambar, status, tanggal_pelaksanaan) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'isssss', $divisiId, $nama, $deskripsi, $gambar, $status, $tanggal);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to create proker.', null, 500);
    }

    json_response(true, 'Proker created.', ['id_proker' => $id]);
}

if ($method === 'PUT') {
    require_admin();
    $data = read_json_body();
    $id = int_or_null($_GET['id'] ?? null) ?? int_or_null($data['id_proker'] ?? null);
    if (!$id) {
        json_response(false, 'Proker id is required.', null, 422);
    }

    $fields = [];
    $values = [];
    $types = '';

    $map = [
        'id_divisi' => 'i',
        'nama_proker' => 's',
        'deskripsi' => 's',
        'gambar' => 's',
        'status' => 's',
        'tanggal_pelaksanaan' => 's',
    ];

    foreach ($map as $key => $type) {
        if (array_key_exists($key, $data)) {
            $fields[] = $key . ' = ?';
            $values[] = $type === 'i' ? (int)$data[$key] : trim((string)$data[$key]);
            $types .= $type;
        }
    }

    if (!$fields) {
        json_response(false, 'No fields to update.', null, 422);
    }

    $types .= 'i';
    $values[] = $id;
    $sql = 'UPDATE proker SET ' . implode(', ', $fields) . ' WHERE id_proker = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to update proker.', null, 500);
    }

    json_response(true, 'Proker updated.', ['id_proker' => $id]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = int_or_null($_GET['id'] ?? null);
    if (!$id) {
        json_response(false, 'Proker id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM proker WHERE id_proker = ?');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to delete proker.', null, 500);
    }

    json_response(true, 'Proker deleted.');
}

json_response(false, 'Method not allowed.', null, 405);
