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
    $nama = trim($_POST['nama'] ?? '');
    $divisiId = filter_var($_POST['id_divisi'] ?? null, FILTER_VALIDATE_INT);

    if ($action === 'create') {
        if ($nama === '' || !$divisiId) {
            pushFlash('error', 'Nama dan divisi wajib diisi.');
        } else {
            $uploadResult = handleImageUpload('foto_member', __DIR__ . '/../../asset/img');
            if (!$uploadResult['ok']) {
                pushFlash('error', $uploadResult['message']);
                redirectBack();
            }
            $foto = $uploadResult['path'] ?? '';
            $stmt = mysqli_prepare($conn, 'INSERT INTO member (nama, id_divisi, foto_member) VALUES (?, ?, ?)');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sis', $nama, $divisiId, $foto);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                pushFlash('success', 'Member baru berhasil ditambahkan.');
            }
        }
        redirectBack();
    }

    if ($action === 'update') {
        $id = filter_var($_POST['id_member'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $nama === '' || !$divisiId) {
            pushFlash('error', 'Data member tidak valid.');
        } else {
            $existingFoto = trim($_POST['existing_foto_member'] ?? '');
            $uploadResult = handleImageUpload('foto_member', __DIR__ . '/../../asset/img', $existingFoto ?: null);
            if (!$uploadResult['ok']) {
                pushFlash('error', $uploadResult['message']);
                redirectBack();
            }
            $foto = $uploadResult['path'] ?? $existingFoto;
            $stmt = mysqli_prepare($conn, 'UPDATE member SET nama = ?, id_divisi = ?, foto_member = ? WHERE id_member = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sisi', $nama, $divisiId, $foto, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                pushFlash('success', 'Member berhasil diperbarui.');
            }
        }
        redirectBack();
    }

    if ($action === 'delete') {
        $id = filter_var($_POST['id_member'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            $fotoPath = null;
            $stmtFoto = mysqli_prepare($conn, 'SELECT foto_member FROM member WHERE id_member = ?');
            if ($stmtFoto) {
                mysqli_stmt_bind_param($stmtFoto, 'i', $id);
                mysqli_stmt_execute($stmtFoto);
                $resultFoto = mysqli_stmt_get_result($stmtFoto);
                $rowFoto = mysqli_fetch_assoc($resultFoto);
                $fotoPath = $rowFoto['foto_member'] ?? null;
                mysqli_free_result($resultFoto);
                mysqli_stmt_close($stmtFoto);
            }

            $stmt = mysqli_prepare($conn, 'DELETE FROM member WHERE id_member = ?');
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
                pushFlash('success', 'Member berhasil dihapus.');
            }
        } else {
            pushFlash('error', 'Member tidak ditemukan.');
        }
        redirectBack();
    }
}

$flashMessages = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

$divisiResult = mysqli_query($conn, 'SELECT id_divisi, nama_divisi FROM divisi ORDER BY nama_divisi ASC');
$divisiList = $divisiResult ? mysqli_fetch_all($divisiResult, MYSQLI_ASSOC) : [];
if ($divisiResult) {
    mysqli_free_result($divisiResult);
}

$memberResult = mysqli_query($conn, 'SELECT m.*, d.nama_divisi FROM member m LEFT JOIN divisi d ON d.id_divisi = m.id_divisi ORDER BY m.nama ASC');
$memberList = $memberResult ? mysqli_fetch_all($memberResult, MYSQLI_ASSOC) : [];
if ($memberResult) {
    mysqli_free_result($memberResult);
}

$editMember = null;
if (isset($_GET['edit_member'])) {
    $editId = filter_input(INPUT_GET, 'edit_member', FILTER_VALIDATE_INT);
    if ($editId) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM member WHERE id_member = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $editId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $editMember = mysqli_fetch_assoc($result) ?: null;
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        }
    }
}

$adminName = h($_SESSION['admin_username'] ?? 'Admin');
$initial = strtoupper(substr($adminName, 0, 1));
$activePage = 'member';
$navLinks = [
    ['label' => 'Dashboard', 'href' => '../dashboard.php', 'key' => 'dashboard'],
    ['label' => 'Divisi', 'href' => '../divisi/index.php', 'key' => 'divisi'],
    ['label' => 'Event', 'href' => '../event/index.php', 'key' => 'event'],
    ['label' => 'Member', 'href' => 'index.php', 'key' => 'member'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50 min-h-screen text-slate-800">
    <header class="bg-white border-b border-orange-100 shadow-sm sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap gap-4 items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-[0.4em] text-orange-500">SEMA</p>
                <h1 class="text-2xl font-semibold text-slate-900">Kelola Member</h1>
                <p class="text-sm text-slate-500">Atur struktur tim dan anggota aktif</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900"><?php echo $adminName; ?></p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">
                    <?php echo $initial; ?>
                </div>
                <a href="../dashboard.php?logout=1" class="px-4 py-2 text-sm font-semibold rounded-full bg-orange-500 text-white hover:bg-orange-600 transition">Keluar</a>
            </div>
        </div>
        <nav class="bg-white border-t border-orange-100">
            <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap gap-2">
                <?php foreach ($navLinks as $link): ?>
                    <?php $isActive = $link['key'] === $activePage; ?>
                    <a href="<?php echo $link['href']; ?>" class="px-4 py-2 text-sm font-semibold rounded-full transition <?php echo $isActive ? 'bg-orange-500 text-white' : 'border border-orange-200 text-orange-600 hover:bg-orange-50'; ?>"><?php echo $link['label']; ?></a>
                <?php endforeach; ?>
            </div>
        </nav>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 space-y-8">
        <?php foreach ($flashMessages as $flash): ?>
            <div class="rounded-2xl px-4 py-3 text-sm border <?php echo $flash['type'] === 'success' ? 'bg-orange-50 border-orange-200 text-orange-700' : 'bg-white border-rose-200 text-rose-600'; ?>">
                <?php echo h($flash['message']); ?>
            </div>
        <?php endforeach; ?>

        <section class="bg-white border border-orange-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-orange-400">Form</p>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        <?php echo $editMember ? 'Perbarui Member' : 'Tambah Member'; ?>
                    </h2>
                </div>
                <?php if ($editMember): ?>
                    <a href="index.php" class="text-sm font-semibold text-orange-600 hover:underline">Batal edit</a>
                <?php endif; ?>
            </div>
            <form action="index.php" method="post" class="grid gap-4" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $editMember ? 'update' : 'create'; ?>" />
                <?php if ($editMember): ?>
                    <input type="hidden" name="id_member" value="<?php echo (int)$editMember['id_member']; ?>" />
                    <input type="hidden" name="existing_foto_member" value="<?php echo h($editMember['foto_member']); ?>" />
                <?php else: ?>
                    <input type="hidden" name="existing_foto_member" value="" />
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo $editMember ? h($editMember['nama']) : ''; ?>" class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Divisi</label>
                    <select name="id_divisi" class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50" required>
                        <option value="">Pilih Divisi</option>
                        <?php foreach ($divisiList as $divisi): ?>
                            <option value="<?php echo (int)$divisi['id_divisi']; ?>" <?php echo $editMember && (int)$editMember['id_divisi'] === (int)$divisi['id_divisi'] ? 'selected' : ''; ?>>
                                <?php echo h($divisi['nama_divisi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Member</label>
                    <input type="file" name="foto_member" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    <p class="text-xs text-slate-500 mt-1">Format JPG, PNG, WEBP &bull; Maks 2MB</p>
                    <?php if ($editMember && $editMember['foto_member']): ?>
                        <p class="text-xs text-slate-500 mt-1">File saat ini: <?php echo h($editMember['foto_member']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl font-semibold text-white bg-orange-500 hover:bg-orange-600 transition">
                        <?php echo $editMember ? 'Simpan Perubahan' : 'Tambah Member'; ?>
                    </button>
                </div>
            </form>
        </section>

        <section class="bg-white border border-orange-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-orange-400">Daftar</p>
                    <h2 class="text-xl font-semibold text-slate-900">Member Terdaftar</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-widest text-slate-500">
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">Divisi</th>
                            <th class="pb-3">Foto</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-50">
                        <?php foreach ($memberList as $member): ?>
                            <tr class="hover:bg-orange-50/60">
                                <td class="py-3 font-semibold text-slate-900"><?php echo h($member['nama']); ?></td>
                                <td class="py-3 text-sm text-slate-700"><?php echo h($member['nama_divisi'] ?? '-'); ?></td>
                                <td class="py-3 text-xs text-slate-500"><?php echo h($member['foto_member']); ?></td>
                                <td class="py-3 text-right space-x-2">
                                    <a href="index.php?edit_member=<?php echo (int)$member['id_member']; ?>" class="text-sm font-semibold text-orange-600 hover:underline">Edit</a>
                                    <form action="index.php" method="post" class="inline" onsubmit="return confirm('Hapus member ini?');">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="id_member" value="<?php echo (int)$member['id_member']; ?>" />
                                        <button type="submit" class="text-sm font-semibold text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$memberList): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500">Belum ada data member.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>
