<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

function pushFlash(string $type, string $message): void {
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message
    ];
}

function redirectBack(): void {
    header('Location: index.php');
    exit;
}

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action      = $_POST['action'] ?? '';
    $nama_proker = trim($_POST['nama_proker'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $id_divisi   = filter_var($_POST['id_divisi'] ?? null, FILTER_VALIDATE_INT);

    // UPLOAD GAMBAR
    $gambar = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $gambar = time() . '_' . uniqid() . '.' . $ext;

            move_uploaded_file(
                $_FILES['gambar']['tmp_name'],
                __DIR__ . '/../../uploads/proker/' . $gambar
            );
        }
    }

    // CREATE
    if ($action === 'create') {

        if ($nama_proker === '' || !$id_divisi) {

            pushFlash('error', 'Nama program kerja dan divisi wajib diisi.');

        } else {

            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO proker (id_divisi, nama_proker, deskripsi, gambar) VALUES (?,?,?,?)'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'isss',
                $id_divisi,
                $nama_proker,
                $deskripsi,
                $gambar
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            pushFlash('success', 'Program kerja berhasil ditambahkan.');
        }

        redirectBack();
    }

    // UPDATE
    if ($action === 'update') {

        $id = filter_var($_POST['id_proker'] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $nama_proker === '' || !$id_divisi) {

            pushFlash('error', 'Data tidak valid.');

        } else {

            if ($gambar) {

                $stmt = mysqli_prepare(
                    $conn,
                    'UPDATE proker 
                     SET nama_proker=?, deskripsi=?, gambar=?, id_divisi=? 
                     WHERE id_proker=?'
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'sssii',
                    $nama_proker,
                    $deskripsi,
                    $gambar,
                    $id_divisi,
                    $id
                );

            } else {

                $stmt = mysqli_prepare(
                    $conn,
                    'UPDATE proker 
                     SET nama_proker=?, deskripsi=?, id_divisi=? 
                     WHERE id_proker=?'
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    'ssii',
                    $nama_proker,
                    $deskripsi,
                    $id_divisi,
                    $id
                );
            }

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            pushFlash('success', 'Program kerja berhasil diperbarui.');
        }

        redirectBack();
    }

    // DELETE
    if ($action === 'delete') {

        $id = filter_var($_POST['id_proker'] ?? null, FILTER_VALIDATE_INT);

        if ($id) {

            $stmt = mysqli_prepare(
                $conn,
                'DELETE FROM proker WHERE id_proker=?'
            );

            mysqli_stmt_bind_param($stmt, 'i', $id);

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            pushFlash('success', 'Program kerja berhasil dihapus.');
        }

        redirectBack();
    }
}

$flashMessages = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

$divisiList = mysqli_fetch_all(
    mysqli_query($conn, 'SELECT * FROM divisi ORDER BY nama_divisi ASC'),
    MYSQLI_ASSOC
);

$prokerList = mysqli_fetch_all(
    mysqli_query(
        $conn,
        'SELECT p.*, d.nama_divisi 
         FROM proker p 
         LEFT JOIN divisi d ON d.id_divisi = p.id_divisi 
         ORDER BY d.nama_divisi, p.nama_proker ASC'
    ),
    MYSQLI_ASSOC
);

$editProker = null;

if (isset($_GET['edit_proker'])) {

    $editId = filter_input(INPUT_GET, 'edit_proker', FILTER_VALIDATE_INT);

    if ($editId) {

        $stmt = mysqli_prepare(
            $conn,
            'SELECT * FROM proker WHERE id_proker=?'
        );

        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);

        $editProker = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmt)
        ) ?: null;

        mysqli_stmt_close($stmt);
    }
}

// FILTER
$filterDivisi = filter_input(INPUT_GET, 'divisi', FILTER_VALIDATE_INT);

if ($filterDivisi) {
    $prokerList = array_filter(
        $prokerList,
        fn($p) => $p['id_divisi'] == $filterDivisi
    );
}

$adminName  = h($_SESSION['admin_username'] ?? 'Admin');
$initial    = strtoupper(substr($adminName, 0, 1));
$activePage = 'proker';

$navLinks = [
    ['label' => 'Dashboard', 'href' => '../dashboard.php', 'key' => 'dashboard'],
    ['label' => 'Divisi', 'href' => '../divisi/index.php', 'key' => 'divisi'],
    ['label' => 'Event', 'href' => '../event/index.php', 'key' => 'event'],
    ['label' => 'Member', 'href' => '../member/index.php', 'key' => 'member'],
    ['label' => 'Proker', 'href' => 'index.php', 'key' => 'proker'],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Program Kerja</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-orange-50 min-h-screen text-slate-800">

<header class="bg-white border-b border-orange-100 shadow-sm sticky top-0 z-10">

    <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap gap-4 items-center justify-between">

        <div>
            <p class="text-xs font-semibold tracking-[0.4em] text-orange-500">
                SEMA
            </p>

            <h1 class="text-2xl font-semibold text-slate-900">
                Kelola Program Kerja
            </h1>

            <p class="text-sm text-slate-500">
                Tambah, perbarui, atau hapus program kerja per divisi
            </p>
        </div>

        <div class="flex items-center gap-4">

            <div class="text-right">
                <p class="text-sm font-semibold text-slate-900">
                    <?php echo $adminName; ?>
                </p>

                <p class="text-xs text-slate-500">
                    Administrator
                </p>
            </div>

            <div class="w-11 h-11 rounded-full bg-orange-100 text-orange-600 font-semibold flex items-center justify-center">
                <?php echo $initial; ?>
            </div>

            <a href="../dashboard.php?logout=1"
               class="px-4 py-2 text-sm font-semibold rounded-full bg-orange-500 text-white hover:bg-orange-600 transition">
                Keluar
            </a>

        </div>

    </div>

</header>

<main class="max-w-6xl mx-auto px-4 py-8 space-y-8">

    <?php foreach ($flashMessages as $flash): ?>

        <div class="rounded-2xl px-4 py-3 text-sm border <?php echo $flash['type'] === 'success'
            ? 'bg-orange-50 border-orange-200 text-orange-700'
            : 'bg-white border-rose-200 text-rose-600'; ?>">

            <?php echo h($flash['message']); ?>

        </div>

    <?php endforeach; ?>

    <!-- FORM -->
    <section class="bg-white border border-orange-100 rounded-3xl p-6 shadow-sm">

        <div class="flex items-center justify-between mb-6">

            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-orange-400">
                    Form
                </p>

                <h2 class="text-2xl font-semibold text-slate-900">
                    <?php echo $editProker ? 'Edit Program Kerja' : 'Tambah Program Kerja'; ?>
                </h2>
            </div>

            <?php if ($editProker): ?>

                <a href="index.php"
                   class="text-sm font-semibold text-orange-600 hover:underline">
                    Batal edit
                </a>

            <?php endif; ?>

        </div>

        <form action="index.php"
              method="post"
              enctype="multipart/form-data"
              class="grid gap-4 md:grid-cols-3">

            <input type="hidden"
                   name="action"
                   value="<?php echo $editProker ? 'update' : 'create'; ?>">

            <?php if ($editProker): ?>

                <input type="hidden"
                       name="id_proker"
                       value="<?php echo (int)$editProker['id_proker']; ?>">

            <?php endif; ?>

            <!-- NAMA -->
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Nama Program Kerja
                </label>

                <input
                    type="text"
                    name="nama_proker"
                    value="<?php echo $editProker ? h($editProker['nama_proker']) : ''; ?>"
                    class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50"
                    required
                >

            </div>

            <!-- DIVISI -->
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Divisi
                </label>

                <select
                    name="id_divisi"
                    class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50"
                    required
                >

                    <option value="">-- Pilih Divisi --</option>

                    <?php foreach ($divisiList as $div): ?>

                        <option
                            value="<?php echo $div['id_divisi']; ?>"
                            <?php echo ($editProker && $editProker['id_divisi'] == $div['id_divisi']) ? 'selected' : ''; ?>
                        >
                            <?php echo h($div['nama_divisi']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- BUTTON -->
            <div class="flex items-end">

                <button
                    type="submit"
                    class="w-full px-4 py-3 rounded-2xl font-semibold text-white bg-orange-500 hover:bg-orange-600 transition"
                >
                    <?php echo $editProker ? 'Simpan Perubahan' : 'Tambah Proker'; ?>
                </button>

            </div>

            <!-- DESKRIPSI -->
            <div class="md:col-span-3">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Deskripsi Program Kerja
                </label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50 resize-none"
                ><?php echo $editProker ? h($editProker['deskripsi'] ?? '') : ''; ?></textarea>

            </div>

            <!-- GAMBAR -->
            <div class="md:col-span-3">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Gambar Program Kerja
                </label>

                <input
                    type="file"
                    name="gambar"
                    accept="image/*"
                    class="w-full px-4 py-3 rounded-2xl border border-orange-100 bg-orange-50"
                >

                <?php if ($editProker && !empty($editProker['gambar'])): ?>

                    <img
                        src="../../uploads/proker/<?php echo h($editProker['gambar']); ?>"
                        class="mt-4 w-40 rounded-2xl border border-orange-100"
                    >

                <?php endif; ?>

            </div>

        </form>

    </section>

</main>

</body>
</html>