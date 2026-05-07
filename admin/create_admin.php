<!-- <?php
require '../config/database.php';

$username = 'edwardchristi4n';
$password_plain = 'AdminSenatMahasiswa2025_!';

$hash = password_hash($password_plain, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO admin (username, password) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
mysqli_stmt_execute($stmt);

echo "Admin berhasil dibuat"; -->
