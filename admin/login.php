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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						night: "#050505",
						midnight: "#0B0B0F",
						ember: "#F15A29",
						tide: "#0FD7D6",
						blush: "#F4DBD7",
					},
					fontFamily: {
						display: ['"Playfair Display"', "serif"],
						body: ['"Space Grotesk"', "sans-serif"],
					},
				},
			},
		};
	</script>
	<style>
		::-webkit-scrollbar { width: 8px; }
		::-webkit-scrollbar-track { background: #0a0a0a; }
		::-webkit-scrollbar-thumb { background: #0FD7D6; border-radius: 5px; }
		.clean-panel { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); }
	</style>
</head>

<body class="min-h-screen bg-night text-white font-body flex items-center justify-center p-4">
	<div class="relative w-full max-w-md">
		<div class="absolute inset-0 blur-3xl bg-ember/30 rounded-[32px]"></div>
		<div class="relative w-full glass-panel rounded-[28px] shadow-2xl shadow-black/50 p-8">
			<div class="text-center mb-8">
				<p class="text-xs font-semibold uppercase tracking-[0.4em] text-white/60">SEMA ADMIN</p>
				<h1 class="font-display text-3xl font-semibold mt-2">Masuk <span class="text-gradient">Dashboard</span></h1>
				<p class="text-sm text-white/60 mt-1">Gunakan akun admin yang terdaftar</p>
			</div>
			<?php if ($error !== ''): ?>
				<div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 text-rose-200 px-4 py-3 text-sm">
					<?= htmlspecialchars($error); ?>
				</div>
			<?php endif; ?>
			<form method="post" class="space-y-5">
				<div>
					<label class="block text-sm font-semibold text-white/80 mb-2">Username</label>
					<input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 focus:outline-none focus:ring-2 focus:ring-tide/40" autocomplete="username" required />
				</div>
				<div>
					<label class="block text-sm font-semibold text-white/80 mb-2">Password</label>
					<input type="password" name="password" class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 focus:outline-none focus:ring-2 focus:ring-tide/40" autocomplete="current-password" required />
				</div>
				<button type="submit" class="w-full inline-flex justify-center items-center rounded-2xl bg-gradient-to-r from-ember to-tide py-3 text-night font-semibold tracking-wide shadow-lg shadow-ember/30 transition">
					Masuk
				</button>
			</form>
			<p class="text-xs text-center text-white/40 mt-8">&copy; <?= date('Y'); ?> Senat Mahasiswa</p>
		</div>
	</div>
</body>

</html>
