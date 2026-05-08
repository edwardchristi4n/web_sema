<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

function pushFlash(string $type, string $message): void
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function redirectBack(): void
{
    header('Location: index.php');
    exit;
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function handleImageUpload(string $fieldName, string $targetDir, ?string $existingPath = null): array
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'path' => $existingPath];
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'message' => 'Gagal mengunggah gambar.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['ok' => false, 'message' => 'Ukuran gambar maksimal 2MB.'];
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
        return ['ok' => false, 'message' => 'Format gambar harus JPG, PNG, atau WEBP.'];
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    try {
        $token = bin2hex(random_bytes(8));
    } catch (Exception $e) {
        $token = str_replace('.', '', uniqid('', true));
    }

    $filename = 'img_' . $token . $allowed[$mime];
    $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'message' => 'Gagal menyimpan file gambar.'];
    }

    if ($existingPath) {
        $oldFile = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($existingPath);
        if (is_file($oldFile)) {
            @unlink($oldFile);
        }
    }

    return ['ok' => true, 'path' => '/asset/img/' . $filename];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $isValidDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) === 1;

    if ($action === 'create') {
        if ($judul === '' || !$isValidDate) {
            pushFlash('error', 'Judul dan tanggal event wajib diisi.');
        } else {
            $uploadResult = handleImageUpload('foto_event', __DIR__ . '/../../asset/img');
            if (!$uploadResult['ok']) {
                pushFlash('error', $uploadResult['message']);
                redirectBack();
            }
            $foto = $uploadResult['path'] ?? '';
            $stmt = mysqli_prepare($conn, 'INSERT INTO event (judul, deskripsi, tanggal, foto_event) VALUES (?, ?, ?, ?)');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssss', $judul, $deskripsi, $tanggal, $foto);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                pushFlash('success', 'Event baru berhasil ditambahkan.');
            }
        }
        redirectBack();
    }

    if ($action === 'update') {
        $id = filter_var($_POST['id_event'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $judul === '' || !$isValidDate) {
            pushFlash('error', 'Data event tidak valid.');
        } else {
            $existingFoto = trim($_POST['existing_foto_event'] ?? '');
            $uploadResult = handleImageUpload('foto_event', __DIR__ . '/../../asset/img', $existingFoto ?: null);
            if (!$uploadResult['ok']) {
                pushFlash('error', $uploadResult['message']);
                redirectBack();
            }
            $foto = $uploadResult['path'] ?? $existingFoto;
            $stmt = mysqli_prepare($conn, 'UPDATE event SET judul = ?, deskripsi = ?, tanggal = ?, foto_event = ? WHERE id_event = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssi', $judul, $deskripsi, $tanggal, $foto, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                pushFlash('success', 'Event berhasil diperbarui.');
            }
        }
        redirectBack();
    }

    if ($action === 'delete') {
        $id = filter_var($_POST['id_event'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            $fotoPath = null;
            $stmtFoto = mysqli_prepare($conn, 'SELECT foto_event FROM event WHERE id_event = ?');
            if ($stmtFoto) {
                mysqli_stmt_bind_param($stmtFoto, 'i', $id);
                mysqli_stmt_execute($stmtFoto);
                $resultFoto = mysqli_stmt_get_result($stmtFoto);
                $rowFoto = mysqli_fetch_assoc($resultFoto);
                $fotoPath = $rowFoto['foto_event'] ?? null;
                mysqli_free_result($resultFoto);
                mysqli_stmt_close($stmtFoto);
            }

            $stmt = mysqli_prepare($conn, 'DELETE FROM event WHERE id_event = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                if ($fotoPath) {
                    $fullPath = __DIR__ . '/../../asset/img/' . basename($fotoPath);
                    if (is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }
                pushFlash('success', 'Event berhasil dihapus.');
            }
        } else {
            pushFlash('error', 'Event tidak ditemukan.');
        }
        redirectBack();
    }
}

$flashMessages = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

$eventResult = mysqli_query($conn, 'SELECT * FROM event ORDER BY tanggal DESC, id_event DESC');
$eventList = $eventResult ? mysqli_fetch_all($eventResult, MYSQLI_ASSOC) : [];
if ($eventResult) {
    mysqli_free_result($eventResult);
}

$editEvent = null;
if (isset($_GET['edit_event'])) {
    $editId = filter_input(INPUT_GET, 'edit_event', FILTER_VALIDATE_INT);
    if ($editId) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM event WHERE id_event = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $editId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editEvent = mysqli_fetch_assoc($result) ?: null;
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        }
    }
}

$adminName = h($_SESSION['admin_username'] ?? 'Admin');
$initial = strtoupper(substr($adminName, 0, 1));
$activePage = 'event';
$navLinks = [
    ['label' => 'Dashboard', 'href' => '../dashboard.php', 'key' => 'dashboard'],
    ['label' => 'Divisi', 'href' => '../divisi/index.php', 'key' => 'divisi'],
    ['label' => 'Event', 'href' => 'index.php', 'key' => 'event'],
    ['label' => 'Member', 'href' => '../member/index.php', 'key' => 'member'],
    ['label' => 'Proker', 'href' => '../proker/index.php', 'key' => 'proker'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Event</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        .glass-panel { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(18px); }
    </style>
</head>

<body class="bg-night min-h-screen text-white font-body">
    <div class="min-h-screen lg:flex">
        <aside class="w-full lg:w-72 lg:min-h-screen bg-night/80 border-b lg:border-b-0 lg:border-r border-white/10 backdrop-blur-xl">
            <div class="px-6 py-6 flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-ember to-tide text-night font-bold flex items-center justify-center">SE</div>
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-white/50">SEMA</p>
                    <p class="font-display text-lg">Admin Panel</p>
                </div>
            </div>
            <nav class="px-4 pb-6 space-y-2">
                <?php foreach ($navLinks as $link): ?>
                    <?php $isActive = $link['key'] === $activePage; ?>
                    <a href="<?php echo $link['href']; ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl border transition <?php echo $isActive ? 'bg-white/10 text-white border-white/20' : 'border-transparent text-white/60 hover:text-white hover:bg-white/5'; ?>">
                        <span class="w-2 h-2 rounded-full <?php echo $isActive ? 'bg-tide' : 'bg-white/20'; ?>"></span>
                        <?php echo $link['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="mt-auto px-6 pb-6">
                <div class="glass-panel rounded-2xl p-4 flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-white/10 text-white font-semibold flex items-center justify-center"><?php echo $initial; ?></div>
                    <div>
                        <p class="text-sm font-semibold"><?php echo $adminName; ?></p>
                        <p class="text-xs text-white/50">Administrator</p>
                    </div>
                </div>
                <a href="../dashboard.php?logout=1" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-gradient-to-r from-ember to-tide text-night font-semibold">Keluar</a>
            </div>
        </aside>

        <div class="flex-1">
            <header class="sticky top-0 z-10 border-b border-white/10 bg-night/70 backdrop-blur-xl">
                <div class="px-6 py-5 flex flex-wrap gap-4 items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-white/50">SEMA ADMIN</p>
                        <h1 class="font-display text-2xl md:text-3xl font-semibold">Kelola Event</h1>
                        <p class="text-sm text-white/60">Atur agenda kampus dan organisasi</p>
                    </div>
                    <?php if ($editEvent): ?>
                        <a href="index.php" class="px-4 py-2 rounded-full glass-panel text-sm font-semibold text-white/80 hover:text-white">Batal edit</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="px-6 py-8 space-y-8">
                <?php foreach ($flashMessages as $flash): ?>
                    <div class="rounded-2xl px-4 py-3 text-sm border <?php echo $flash['type'] === 'success' ? 'bg-ember/10 border-ember/30 text-ember' : 'bg-rose-500/10 border-rose-500/30 text-rose-200'; ?>">
                        <?php echo h($flash['message']); ?>
                    </div>
                <?php endforeach; ?>

                <section class="glass-panel rounded-[2rem] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-white/50">Form</p>
                            <h2 class="text-2xl font-semibold text-white">
                                <?php echo $editEvent ? 'Perbarui Event' : 'Tambah Event'; ?>
                            </h2>
                        </div>
                    </div>
                    <form action="index.php" method="post" class="grid gap-4" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $editEvent ? 'update' : 'create'; ?>" />
                        <?php if ($editEvent): ?>
                            <input type="hidden" name="id_event" value="<?php echo (int)$editEvent['id_event']; ?>" />
                            <input type="hidden" name="existing_foto_event" value="<?php echo h($editEvent['foto_event']); ?>" />
                        <?php else: ?>
                            <input type="hidden" name="existing_foto_event" value="" />
                        <?php endif; ?>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Judul Event</label>
                                <input type="text" name="judul" value="<?php echo $editEvent ? h($editEvent['judul']) : ''; ?>" class="w-full px-4 py-3 rounded-2xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-tide/40 bg-white/5" required />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-2">Tanggal</label>
                                <input type="date" name="tanggal" value="<?php echo $editEvent ? h($editEvent['tanggal']) : ''; ?>" class="w-full px-4 py-3 rounded-2xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-tide/40 bg-white/5" required />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-white/80 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 rounded-2xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-tide/40 bg-white/5"><?php echo $editEvent ? h($editEvent['deskripsi']) : ''; ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-white/80 mb-2">Foto Event</label>
                            <input type="file" name="foto_event" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 focus:outline-none focus:ring-2 focus:ring-tide/40" />
                            <p class="text-xs text-white/50 mt-1">Format JPG, PNG, WEBP &bull; Maks 2MB</p>
                            <?php if ($editEvent && $editEvent['foto_event']): ?>
                                <p class="text-xs text-white/50 mt-1">File saat ini: <?php echo h($editEvent['foto_event']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-2xl font-semibold text-night bg-gradient-to-r from-ember to-tide hover:shadow-[0_0_30px_rgba(241,90,41,0.35)] transition">
                                <?php echo $editEvent ? 'Simpan Perubahan' : 'Tambah Event'; ?>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="glass-panel rounded-[2rem] p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-white/50">Daftar</p>
                            <h2 class="text-xl font-semibold text-white">Event Terdaftar</h2>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-white/80">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-widest text-white/40">
                                    <th class="pb-3">Event</th>
                                    <th class="pb-3">Tanggal</th>
                                    <th class="pb-3">Foto</th>
                                    <th class="pb-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <?php foreach ($eventList as $event): ?>
                                    <tr class="odd:bg-white/5 hover:bg-white/10">
                                        <td class="py-3">
                                            <p class="font-semibold text-white"><?php echo h($event['judul']); ?></p>
                                            <p class="text-xs text-white/50"><?php echo h($event['deskripsi']); ?></p>
                                        </td>
                                        <td class="py-3 text-sm text-white/70"><?php echo h($event['tanggal']); ?></td>
                                        <td class="py-3 text-xs text-white/50"><?php echo h($event['foto_event']); ?></td>
                                        <td class="py-3 text-right space-x-2">
                                            <a href="index.php?edit_event=<?php echo (int)$event['id_event']; ?>" class="text-sm font-semibold text-tide hover:underline">Edit</a>
                                            <form action="index.php" method="post" class="inline" onsubmit="return confirm('Hapus event ini?');">
                                                <input type="hidden" name="action" value="delete" />
                                                <input type="hidden" name="id_event" value="<?php echo (int)$event['id_event']; ?>" />
                                                <button type="submit" class="text-sm font-semibold text-rose-300 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$eventList): ?>
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-white/50">Belum ada event terdaftar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
