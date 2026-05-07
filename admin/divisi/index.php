<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

function pushFlash(string $type, string $message): void {
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) $_SESSION['flash'] = [];
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}
function redirectBack(): void { header('Location: index.php'); exit; }
function h(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }

// Handle upload foto koor
function uploadFoto(string $inputName, string $folder): ?string {
    if (empty($_FILES[$inputName]['name'])) return null;
    $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) return null;
    $filename = 'koor_' . time() . '_' . rand(100,999) . '.' . $ext;
    $uploadDir = __DIR__ . '/../../asset/img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $filename)) {
        return '/asset/img/' . $filename;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $nama      = trim($_POST['nama_divisi'] ?? '');
    $visi      = trim($_POST['visi'] ?? '');
    $misi      = trim($_POST['misi'] ?? '');
    $nama_koor = trim($_POST['nama_koor'] ?? '');

    if ($action === 'create') {
        if ($nama === '') { pushFlash('error', 'Nama divisi wajib diisi.'); }
        else {
            $foto_koor = uploadFoto('foto_koor', 'asset/img/');
            $stmt = mysqli_prepare($conn, 'INSERT INTO divisi (nama_divisi, visi, misi, nama_koor, foto_koor) VALUES (?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'sssss', $nama, $visi, $misi, $nama_koor, $foto_koor);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            pushFlash('success', 'Divisi baru berhasil ditambahkan.');
        }
        redirectBack();
    }

    if ($action === 'update') {
        $id = filter_var($_POST['id_divisi'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $nama === '') { pushFlash('error', 'Data tidak valid.'); }
        else {
            $foto_koor = uploadFoto('foto_koor', 'asset/img/');
            if ($foto_koor) {
                $stmt = mysqli_prepare($conn, 'UPDATE divisi SET nama_divisi=?, visi=?, misi=?, nama_koor=?, foto_koor=? WHERE id_divisi=?');
                mysqli_stmt_bind_param($stmt, 'sssssi', $nama, $visi, $misi, $nama_koor, $foto_koor, $id);
            } else {
                $stmt = mysqli_prepare($conn, 'UPDATE divisi SET nama_divisi=?, visi=?, misi=?, nama_koor=? WHERE id_divisi=?');
                mysqli_stmt_bind_param($stmt, 'ssssi', $nama, $visi, $misi, $nama_koor, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            pushFlash('success', 'Divisi berhasil diperbarui.');
        }
        redirectBack();
    }

    if ($action === 'delete') {
        $id = filter_var($_POST['id_divisi'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM divisi WHERE id_divisi = ?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (mysqli_stmt_execute($stmt)) pushFlash('success', 'Divisi berhasil dihapus.');
            else pushFlash('error', 'Divisi tidak dapat dihapus karena masih digunakan.');
            mysqli_stmt_close($stmt);
        } else pushFlash('error', 'Divisi tidak ditemukan.');
        redirectBack();
    }
}

$flashMessages = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

$divisiList = mysqli_fetch_all(mysqli_query($conn, 'SELECT * FROM divisi ORDER BY nama_divisi ASC'), MYSQLI_ASSOC);

$editDivisi = null;
if (isset($_GET['edit_divisi'])) {
    $editId = filter_input(INPUT_GET, 'edit_divisi', FILTER_VALIDATE_INT);
    if ($editId) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM divisi WHERE id_divisi = ?');
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $editDivisi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
        mysqli_stmt_close($stmt);
    }
}

$adminName = h($_SESSION['admin_username'] ?? 'Admin');
$initial   = strtoupper(substr($adminName, 0, 1));
$activePage = 'divisi';
$navLinks = [
    ['label' => 'Dashboard', 'href' => '../dashboard.php',      'key' => 'dashboard'],
    ['label' => 'Divisi',    'href' => 'index.php',             'key' => 'divisi'],
    ['label' => 'Event',     'href' => '../event/index.php',    'key' => 'event'],
    ['label' => 'Member',    'href' => '../member/index.php',   'key' => 'member'],
    ['label' => 'Proker',    'href' => '../proker/index.php',   'key' => 'proker'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Divisi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 min-h-screen text-slate-800">
    <header class="bg-white border-b border-orange-100 shadow-sm sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap gap-4 items-center justify-between">
            <div>
                <p class="text-xs font-semibold tracking-[0.4em] text-orange-500">SEMA</p>
                <h1 class="text-2xl font-semibold text-slate-900">Kelola Divisi</h1>
                <p class="text-sm text-slate-500">Tambah, perbarui, atau hapus data divisi</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900"><?php echo $adminName; ?></p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center"><?php echo $initial; ?></div>
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

        <!-- Form Tambah / Edit -->
        <section class="bg-white border border-orange-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-orange-400">Form</p>
                    <h2 class="text-2xl font-semibold text-slate-900"><?php echo $editDivisi ? 'Perbarui Divisi' : 'Tambah Divisi'; ?></h2>
                </div>
                <?php if ($editDivisi): ?>
                    <a href="index.php" class="text-sm font-semibold text-orange-600 hover:underline">Batal edit</a>
                <?php endif; ?>
            </div>
            <form action="index.php" method="post" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="action" value="<?php echo $editDivisi ? 'update' : 'create'; ?>">
                <?php if ($editDivisi): ?>
                    <input type="hidden" name="id_divisi" value="<?php echo (int)$editDivisi['id_divisi']; ?>">
                <?php endif; ?>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Divisi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_divisi" value="<?php echo $editDivisi ? h($editDivisi['nama_divisi']) : ''; ?>" class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Koordinator</label>
                        <input type="text" name="nama_koor" value="<?php echo $editDivisi ? h($editDivisi['nama_koor'] ?? '') : ''; ?>" placeholder="Nama koordinator bidang" class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Koordinator</label>
                    <?php if ($editDivisi && !empty($editDivisi['foto_koor'])): ?>
                        <div class="mb-3 flex items-center gap-3">
                            <img src="<?php echo h($editDivisi['foto_koor']); ?>" class="w-16 h-16 rounded-xl object-cover border border-orange-100">
                            <span class="text-sm text-slate-500">Foto saat ini. Upload baru untuk mengganti.</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto_koor" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Visi</label>
                    <textarea name="visi" rows="3" placeholder="Tuliskan visi bidang ini..." class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50 resize-none"><?php echo $editDivisi ? h($editDivisi['visi'] ?? '') : ''; ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Misi</label>
                    <textarea name="misi" rows="3" placeholder="Tuliskan misi bidang ini..." class="w-full px-4 py-3 rounded-2xl border border-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-400 bg-orange-50 resize-none"><?php echo $editDivisi ? h($editDivisi['misi'] ?? '') : ''; ?></textarea>
                </div>

                <button type="submit" class="w-full px-4 py-3 rounded-2xl font-semibold text-white bg-orange-500 hover:bg-orange-600 transition">
                    <?php echo $editDivisi ? 'Simpan Perubahan' : 'Tambah Divisi'; ?>
                </button>
            </form>
        </section>

        <!-- Tabel Divisi -->
        <section class="bg-white border border-orange-100 rounded-3xl p-6 shadow-sm">
            <div class="mb-4">
                <p class="text-xs uppercase tracking-[0.3em] text-orange-400">Daftar</p>
                <h2 class="text-xl font-semibold text-slate-900">Divisi Terdaftar</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-widest text-slate-500">
                            <th class="pb-3">Nama Divisi</th>
                            <th class="pb-3">Koordinator</th>
                            <th class="pb-3">Visi/Misi</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-50">
                        <?php foreach ($divisiList as $divisi): ?>
                        <tr class="hover:bg-orange-50/60">
                            <td class="py-3 font-semibold text-slate-900"><?php echo h($divisi['nama_divisi']); ?></td>
                            <td class="py-3 text-slate-600">
                                <?php if (!empty($divisi['foto_koor'])): ?>
                                    <div class="flex items-center gap-2">
                                        <img src="<?php echo h($divisi['foto_koor']); ?>" class="w-8 h-8 rounded-full object-cover border border-orange-100">
                                        <span><?php echo h($divisi['nama_koor'] ?? '-'); ?></span>
                                    </div>
                                <?php else: ?>
                                    <?php echo h($divisi['nama_koor'] ?? '-'); ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 text-slate-500">
                                <?php echo !empty($divisi['visi']) ? '<span class="px-2 py-0.5 bg-green-50 text-green-700 rounded-full text-xs">Lengkap</span>' : '<span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full text-xs">Belum diisi</span>'; ?>
                            </td>
                            <td class="py-3 text-right space-x-2">
                                <a href="../../bidang-detail.php?id=<?php echo (int)$divisi['id_divisi']; ?>" target="_blank" class="text-sm font-semibold text-slate-400 hover:text-slate-600">Preview</a>
                                <a href="index.php?edit_divisi=<?php echo (int)$divisi['id_divisi']; ?>" class="text-sm font-semibold text-orange-600 hover:underline">Edit</a>
                                <form action="index.php" method="post" class="inline" onsubmit="return confirm('Hapus divisi ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_divisi" value="<?php echo (int)$divisi['id_divisi']; ?>">
                                    <button type="submit" class="text-sm font-semibold text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (!$divisiList): ?>
                        <tr><td colspan="4" class="py-4 text-center text-slate-500">Belum ada data divisi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>