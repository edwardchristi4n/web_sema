<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['admin_id'])) {
	header('Location: dashboard.php');
	exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');

	if ($username === '' || $password === '') {
		$error = 'Username dan password wajib diisi.';
	} else {
		$stmt = mysqli_prepare($conn, 'SELECT id_admin, username, password FROM admin WHERE username = ? LIMIT 1');
		if ($stmt) {
			mysqli_stmt_bind_param($stmt, 's', $username);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$admin = mysqli_fetch_assoc($result);
			mysqli_stmt_close($stmt);

			if ($admin) {
				$storedPassword = $admin['password'] ?? '';
				$isValid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

				if ($isValid) {
					session_regenerate_id(true);
					$_SESSION['admin_id'] = $admin['id_admin'];
					$_SESSION['admin_username'] = $admin['username'];
					header('Location: dashboard.php');
					exit;
				}
			}
		}

		$error = 'Username atau password salah.';
	}
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Login Admin</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 flex items-center justify-center p-4">
	<div class="relative w-full max-w-md">
		<div class="absolute inset-0 blur-3xl bg-orange-200/40 rounded-[32px]"></div>
		<div class="relative w-full bg-white border border-orange-100 rounded-[28px] shadow-2xl shadow-orange-200/80 p-8">
			<div class="text-center mb-8">
				<p class="text-xs font-semibold uppercase tracking-[0.4em] text-orange-500">SEMA ADMIN</p>
				<h1 class="text-3xl font-semibold text-slate-900 mt-2">Masuk Dashboard</h1>
				<p class="text-sm text-slate-500 mt-1">Gunakan akun admin yang terdaftar</p>
			</div>
			<?php if ($error !== ''): ?>
				<div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 text-rose-600 px-4 py-3 text-sm">
					<?= htmlspecialchars($error); ?>
				</div>
			<?php endif; ?>
			<form method="post" class="space-y-5">
				<div>
					<label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
					<input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400" autocomplete="username" required />
				</div>
				<div>
					<label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
					<input type="password" name="password" class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400" autocomplete="current-password" required />
				</div>
				<button type="submit" class="w-full inline-flex justify-center items-center rounded-2xl bg-orange-500 hover:bg-orange-600 py-3 text-white font-semibold tracking-wide shadow-lg shadow-orange-500/30 transition">
					Masuk
				</button>
			</form>
			<p class="text-xs text-center text-slate-400 mt-8">&copy; <?= date('Y'); ?> Senat Mahasiswa</p>
		</div>
	</div>
</body>

</html>
