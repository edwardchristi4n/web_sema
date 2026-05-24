<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $id = int_or_null($_GET['id'] ?? null);
    $divisiId = int_or_null($_GET['divisi_id'] ?? null);

    if ($id) {
        $stmt = mysqli_prepare($conn, 'SELECT m.*, d.nama_divisi FROM member m LEFT JOIN divisi d ON d.id_divisi = m.id_divisi WHERE m.id_member = ?');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$row) {
            json_response(false, 'Member not found.', null, 404);
        }

        json_response(true, 'Member fetched.', $row);
    }

    if ($divisiId) {
        $stmt = mysqli_prepare($conn, 'SELECT m.*, d.nama_divisi FROM member m LEFT JOIN divisi d ON d.id_divisi = m.id_divisi WHERE m.id_divisi = ? ORDER BY m.nama ASC');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $divisiId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);

        json_response(true, 'Member list fetched.', $rows);
    }

    $result = mysqli_query($conn, 'SELECT m.*, d.nama_divisi FROM member m LEFT JOIN divisi d ON d.id_divisi = m.id_divisi ORDER BY m.nama ASC');
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    if ($result) {
        mysqli_free_result($result);
    }

    json_response(true, 'Member list fetched.', $rows);
}

if ($method === 'POST') {
    require_admin();
    $data = read_json_body();
    require_fields($data, ['nama', 'id_divisi']);

    $nama = trim((string)$data['nama']);
    $divisiId = int_or_null($data['id_divisi']);
    $foto = trim((string)($data['foto_member'] ?? ''));

    if (!$divisiId) {
        json_response(false, 'Divisi id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO member (nama, id_divisi, foto_member) VALUES (?, ?, ?)');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'sis', $nama, $divisiId, $foto);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to create member.', null, 500);
    }

    json_response(true, 'Member created.', ['id_member' => $id]);
}

if ($method === 'PUT') {
    require_admin();
    $data = read_json_body();
    $id = int_or_null($_GET['id'] ?? null) ?? int_or_null($data['id_member'] ?? null);
    if (!$id) {
        json_response(false, 'Member id is required.', null, 422);
    }

    $fields = [];
    $values = [];
    $types = '';

    $map = [
        'nama' => 's',
        'id_divisi' => 'i',
        'foto_member' => 's',
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
    $sql = 'UPDATE member SET ' . implode(', ', $fields) . ' WHERE id_member = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to update member.', null, 500);
    }

    json_response(true, 'Member updated.', ['id_member' => $id]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = int_or_null($_GET['id'] ?? null);
    if (!$id) {
        json_response(false, 'Member id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM member WHERE id_member = ?');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to delete member.', null, 500);
    }

    json_response(true, 'Member deleted.');
}

json_response(false, 'Method not allowed.', null, 405);
