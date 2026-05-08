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
                        <h1 class="font-display text-2xl md:text-3xl font-semibold">Kelola Program Kerja</h1>
                        <p class="text-sm text-white/60">Tambah, perbarui, atau hapus program kerja per divisi</p>
                    </div>
                    <?php if ($editProker): ?>
                        <a href="index.php" class="px-4 py-2 rounded-full glass-panel text-sm font-semibold text-white/80 hover:text-white">Batal edit</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="px-6 py-8 space-y-8">

            <?php foreach ($flashMessages as $flash): ?>

                <div class="rounded-2xl px-4 py-3 text-sm border <?php echo $flash['type'] === 'success'
                    ? 'bg-ember/10 border-ember/30 text-ember'
                    : 'bg-rose-500/10 border-rose-500/30 text-rose-200'; ?>">

                    <?php echo h($flash['message']); ?>

                </div>

            <?php endforeach; ?>

            <!-- FORM -->
            <section class="glass-panel rounded-[2rem] p-6">

        <div class="flex items-center justify-between mb-6">

            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-white/50">
                    Form
                </p>

                <h2 class="text-2xl font-semibold text-white">
                    <?php echo $editProker ? 'Edit Program Kerja' : 'Tambah Program Kerja'; ?>
                </h2>
            </div>

            <?php if ($editProker): ?>

                <a href="index.php"
                   class="text-sm font-semibold text-white/70 hover:text-white">
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

                <label class="block text-sm font-semibold text-white/80 mb-2">
                    Nama Program Kerja
                </label>

                <input
                    type="text"
                    name="nama_proker"
                    value="<?php echo $editProker ? h($editProker['nama_proker']) : ''; ?>"
                    class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 focus:outline-none focus:ring-2 focus:ring-tide/40"
                    required
                >

            </div>

            <!-- DIVISI -->
            <div>

                <label class="block text-sm font-semibold text-white/80 mb-2">
                    Divisi
                </label>

                <select
                    name="id_divisi"
                    class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 focus:outline-none focus:ring-2 focus:ring-tide/40"
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
                    class="w-full px-4 py-3 rounded-2xl font-semibold text-night bg-gradient-to-r from-ember to-tide hover:shadow-[0_0_30px_rgba(241,90,41,0.35)] transition"
                >
                    <?php echo $editProker ? 'Simpan Perubahan' : 'Tambah Proker'; ?>
                </button>

            </div>

            <!-- DESKRIPSI -->
            <div class="md:col-span-3">

                <label class="block text-sm font-semibold text-white/80 mb-2">
                    Deskripsi Program Kerja
                </label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5 resize-none focus:outline-none focus:ring-2 focus:ring-tide/40"
                ><?php echo $editProker ? h($editProker['deskripsi'] ?? '') : ''; ?></textarea>

            </div>

            <!-- GAMBAR -->
            <div class="md:col-span-3">

                <label class="block text-sm font-semibold text-white/80 mb-2">
                    Gambar Program Kerja
                </label>

                <input
                    type="file"
                    name="gambar"
                    accept="image/*"
                    class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-white/5"
                >

                <?php if ($editProker && !empty($editProker['gambar'])): ?>

                    <img
                        src="../../uploads/proker/<?php echo h($editProker['gambar']); ?>"
                        class="mt-4 w-40 rounded-2xl border border-white/10"
                    >

                <?php endif; ?>

            </div>

        </form>

            </section>

            <section class="glass-panel rounded-[2rem] p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-white/50">Daftar</p>
                        <h2 class="text-xl font-semibold text-white">Program Kerja Terdaftar</h2>
                    </div>
                    <form method="get" class="flex items-center gap-3">
                        <select name="divisi" class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm">
                            <option value="">Semua Divisi</option>
                            <?php foreach ($divisiList as $div): ?>
                                <option value="<?php echo $div['id_divisi']; ?>" <?php echo $filterDivisi && (int)$filterDivisi === (int)$div['id_divisi'] ? 'selected' : ''; ?>>
                                    <?php echo h($div['nama_divisi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-full bg-white/10 text-white/80 hover:text-white">Filter</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-white/80">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-widest text-white/40">
                                <th class="pb-3">Program Kerja</th>
                                <th class="pb-3">Divisi</th>
                                <th class="pb-3">Gambar</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <?php foreach ($prokerList as $proker): ?>
                                <tr class="odd:bg-white/5 hover:bg-white/10">
                                    <td class="py-3">
                                        <p class="font-semibold text-white"><?php echo h($proker['nama_proker']); ?></p>
                                        <p class="text-xs text-white/50"><?php echo h($proker['deskripsi']); ?></p>
                                    </td>
                                    <td class="py-3 text-white/60"><?php echo h($proker['nama_divisi'] ?? '-'); ?></td>
                                    <td class="py-3 text-xs text-white/50"><?php echo h($proker['gambar'] ?? '-'); ?></td>
                                    <td class="py-3 text-right space-x-2">
                                        <a href="index.php?edit_proker=<?php echo (int)$proker['id_proker']; ?>" class="text-sm font-semibold text-tide hover:underline">Edit</a>
                                        <form action="index.php" method="post" class="inline" onsubmit="return confirm('Hapus program kerja ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_proker" value="<?php echo (int)$proker['id_proker']; ?>">
                                            <button type="submit" class="text-sm font-semibold text-rose-300 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$prokerList): ?>
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-white/50">Belum ada program kerja terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>