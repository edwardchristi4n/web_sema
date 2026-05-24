<?php
require_once __DIR__ . '/_bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    if (!empty($_SESSION['admin_id'])) {
        json_response(true, 'Authenticated.', [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? 'admin',
        ]);
    }

    json_response(false, 'Not authenticated.', null, 401);
}

if ($method === 'POST') {
    $data = read_json_body();
    $username = trim((string)($data['username'] ?? ''));
    $password = trim((string)($data['password'] ?? ''));

    if ($username === '' || $password === '') {
        json_response(false, 'Username and password are required.', null, 422);
    }

    $stmt = mysqli_prepare($conn, 'SELECT id_admin, username, password FROM admin WHERE username = ? LIMIT 1');
    if (!$stmt) {
        json_response(false, 'Failed to prepare statement.', null, 500);
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if (!$admin) {
        json_response(false, 'Invalid credentials.', null, 401);
    }

    $storedPassword = (string)($admin['password'] ?? '');
    $isValid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

    if (!$isValid) {
        json_response(false, 'Invalid credentials.', null, 401);
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$admin['id_admin'];
    $_SESSION['admin_username'] = $admin['username'] ?? $username;

    json_response(true, 'Login successful.', [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
    ]);
}

if ($method === 'DELETE') {
    session_unset();
    session_destroy();
    json_response(true, 'Logged out.');
}

json_response(false, 'Method not allowed.', null, 405);
