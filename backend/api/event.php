<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $id = int_or_null($_GET['id'] ?? null);
    $limit = int_or_null($_GET['limit'] ?? null);

    if ($id) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM event WHERE id_event = ?');
        if (!$stmt) {
            json_response(false, 'Failed to prepare statement.', null, 500);
        }
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$row) {
            json_response(false, 'Event not found.', null, 404);
        }

        json_response(true, 'Event fetched.', $row);
    }

    $sql = 'SELECT * FROM event ORDER BY tanggal DESC, id_event DESC';
    if ($limit) {
        $sql .= ' LIMIT ' . $limit;
    }

    $result = mysqli_query($conn, $sql);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    if ($result) {
        mysqli_free_result($result);
    }

    json_response(true, 'Event list fetched.', $rows);
}

if ($method === 'POST') {
    require_admin();
    $data = read_json_body();
    require_fields($data, ['judul', 'tanggal']);

    $judul = trim((string)$data['judul']);
    $deskripsi = trim((string)($data['deskripsi'] ?? ''));
    $tanggal = trim((string)$data['tanggal']);
    $lokasi = trim((string)($data['lokasi'] ?? ''));
    $foto = trim((string)($data['foto_event'] ?? ''));

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        json_response(false, 'Tanggal harus format YYYY-MM-DD.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO event (judul, deskripsi, tanggal, lokasi, foto_event) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'sssss', $judul, $deskripsi, $tanggal, $lokasi, $foto);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to create event.', null, 500);
    }

    json_response(true, 'Event created.', ['id_event' => $id]);
}

if ($method === 'PUT') {
    require_admin();
    $data = read_json_body();
    $id = int_or_null($_GET['id'] ?? null) ?? int_or_null($data['id_event'] ?? null);
    if (!$id) {
        json_response(false, 'Event id is required.', null, 422);
    }

    if (array_key_exists('tanggal', $data)) {
        $tanggal = trim((string)$data['tanggal']);
        if ($tanggal !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            json_response(false, 'Tanggal harus format YYYY-MM-DD.', null, 422);
        }
    }

    $fields = [];
    $values = [];
    $types = '';
    $map = [
        'judul' => 's',
        'deskripsi' => 's',
        'tanggal' => 's',
        'lokasi' => 's',
        'foto_event' => 's',
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
    $sql = 'UPDATE event SET ' . implode(', ', $fields) . ' WHERE id_event = ?';
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to update event.', null, 500);
    }

    json_response(true, 'Event updated.', ['id_event' => $id]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = int_or_null($_GET['id'] ?? null);
    if (!$id) {
        json_response(false, 'Event id is required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'DELETE FROM event WHERE id_event = ?');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$ok) {
        json_response(false, 'Failed to delete event.', null, 500);
    }

    json_response(true, 'Event deleted.');
}

json_response(false, 'Method not allowed.', null, 405);
