<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $id = int_or_null($_GET['id'] ?? null);

    if ($id) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM divisi WHERE id_divisi = ?');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$row) {
            json_response(false, 'Divisi not found.', null, 404);
        }

        json_response(true, 'Divisi fetched.', $row);
    }

    $result = mysqli_query($conn, 'SELECT * FROM divisi ORDER BY nama_divisi ASC');
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    if ($result) {
        mysqli_free_result($result);
    }

    json_response(true, 'Divisi list fetched.', $rows);
}

if ($method === 'POST') {
    require_admin();
    $data = read_json_body();
    require_fields($data, ['nama_divisi']);

    $nama = trim((string)$data['nama_divisi']);
    $visi = trim((string)($data['visi'] ?? ''));
    $misi = trim((string)($data['misi'] ?? ''));
    $namaKoor = trim((string)($data['nama_koor'] ?? ''));
    $fotoKoor = trim((string)($data['foto_koor'] ?? ''));

    $stmt = mysqli_prepare($conn, 'INSERT INTO divisi (nama_divisi, visi, misi, nama_koor, foto_koor) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'sssss', $nama, $visi, $misi, $namaKoor, $fotoKoor);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to create divisi.', null, 500);
    }

    json_response(true, 'Divisi created.', ['id_divisi' => $id]);
}

if ($method === 'PUT') {
    require_admin();
    $data = read_json_body();
    $id = int_or_null($_GET['id'] ?? null) ?? int_or_null($data['id_divisi'] ?? null);
    if (!$id) {
        json_response(false, 'Divisi id is required.', null, 422);
    }

    $fields = [];
    $values = [];
    $types = '';

    $map = [
        'nama_divisi' => 's',
        'visi' => 's',
        'misi' => 's',
        'nama_koor' => 's',
        'foto_koor' => 's',
    ];

    foreach ($map as $key => $type) {
        if (array_key_exists($key, $data)) {
            $fields[] = $key . ' = ?';
            $values[] = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
            $types .= $type;
        }
    }

    if (!$fields) {
        json_response(false, 'No fields to update.', null, 422);
    }

    $types .= 'i';
    $values[] = $id;
    $sql = 'UPDATE divisi SET ' . implode(', ', $fields) . ' WHERE id_divisi = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to update divisi.', null, 500);
    }

    json_response(true, 'Divisi updated.', ['id_divisi' => $id]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = int_or_null($_GET['id'] ?? null);
    if (!$id) {
        json_response(false, 'Divisi id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM divisi WHERE id_divisi = ?');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to delete divisi.', null, 500);
    }

    json_response(true, 'Divisi deleted.');
}

json_response(false, 'Method not allowed.', null, 405);
