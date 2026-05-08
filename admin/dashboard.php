<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

function fetchCount(mysqli $conn, string $table): int
{
    $result = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM ' . $table);
    if (!$result) {
        return 0;
    }

    $data = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    return (int)($data['total'] ?? 0);
}

function fetchAll(mysqli $conn, string $query): array
{
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return [];
    }

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    return $rows;
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$counts = [
    'divisi' => fetchCount($conn, 'divisi'),
    'event' => fetchCount($conn, 'event'),
    'member' => fetchCount($conn, 'member'),
];

$recentEvents = fetchAll($conn, 'SELECT id_event, judul, tanggal FROM event ORDER BY tanggal DESC LIMIT 4');
$recentMembers = fetchAll($conn, 'SELECT m.id_member, m.nama, d.nama_divisi FROM member m LEFT JOIN divisi d ON d.id_divisi = m.id_divisi ORDER BY m.id_member DESC LIMIT 5');
$recentDivisi = fetchAll($conn, 'SELECT id_divisi, nama_divisi FROM divisi ORDER BY nama_divisi ASC LIMIT 5');

$adminName = h($_SESSION['admin_username'] ?? 'Admin');
$initial = strtoupper(substr($adminName, 0, 1));
$activePage = 'dashboard';
$navLinks = [
    ['label' => 'Dashboard', 'href' => 'dashboard.php', 'key' => 'dashboard'],
    ['label' => 'Divisi', 'href' => 'divisi/index.php', 'key' => 'divisi'],
    ['label' => 'Event', 'href' => 'event/index.php', 'key' => 'event'],
    ['label' => 'Member', 'href' => 'member/index.php', 'key' => 'member'],
    ['label' => 'Proker', 'href' => 'proker/index.php', 'key' => 'proker'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
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
                    <div class="w-10 h-10 rounded-full bg-white/10 text-white font-semibold flex items-center justify-center">
                        <?php echo $initial; ?>
                    </div>
                    <div>
                        <p class="text-sm font-semibold"><?php echo $adminName; ?></p>
                        <p class="text-xs text-white/50">Administrator</p>
                    </div>
                </div>
                <a href="?logout=1" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-gradient-to-r from-ember to-tide text-night font-semibold">Keluar</a>
            </div>
        </aside>

        <div class="flex-1">
            <header class="sticky top-0 z-10 border-b border-white/10 bg-night/70 backdrop-blur-xl">
                <div class="px-6 py-5 flex flex-wrap gap-4 items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-white/50">SEMA ADMIN</p>
                        <h1 class="font-display text-2xl md:text-3xl font-semibold">Dashboard Admin</h1>
                        <p class="text-sm text-white/60">Mengelola Divisi, Event, dan Member</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 rounded-full glass-panel text-xs uppercase tracking-[0.35em] text-white/60">Overview</span>
                    </div>
                </div>
            </header>

            <main class="px-6 py-8 space-y-8">
                <section class="grid gap-4 md:grid-cols-3">
                    <article class="glass-panel rounded-[2rem] p-6">
                        <p class="text-xs uppercase text-white/50 tracking-[0.3em]">Divisi</p>
                        <div class="flex items-end justify-between mt-3">
                            <h2 class="text-3xl font-semibold text-white"><?php echo $counts['divisi']; ?></h2>
                            <a href="divisi/index.php" class="text-sm font-semibold text-tide hover:underline">Kelola</a>
                        </div>
                        <p class="text-xs text-white/50 mt-1">Total divisi aktif</p>
                    </article>
                    <article class="glass-panel rounded-[2rem] p-6">
                        <p class="text-xs uppercase text-white/50 tracking-[0.3em]">Event</p>
                        <div class="flex items-end justify-between mt-3">
                            <h2 class="text-3xl font-semibold text-white"><?php echo $counts['event']; ?></h2>
                            <a href="event/index.php" class="text-sm font-semibold text-ember hover:underline">Kelola</a>
                        </div>
                        <p class="text-xs text-white/50 mt-1">Event terjadwal</p>
                    </article>
                    <article class="glass-panel rounded-[2rem] p-6">
                        <p class="text-xs uppercase text-white/50 tracking-[0.3em]">Member</p>
                        <div class="flex items-end justify-between mt-3">
                            <h2 class="text-3xl font-semibold text-white"><?php echo $counts['member']; ?></h2>
                            <a href="member/index.php" class="text-sm font-semibold text-blush hover:underline">Kelola</a>
                        </div>
                        <p class="text-xs text-white/50 mt-1">Member aktif</p>
                    </article>
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <div class="glass-panel rounded-[2rem] p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-white/50">Agenda</p>
                                <h2 class="text-xl font-semibold text-white">Event Terbaru</h2>
                            </div>
                            <a href="event/index.php" class="text-sm font-semibold text-tide hover:underline">Lihat semua</a>
                        </div>
                        <ul class="space-y-3">
                            <?php if ($recentEvents): ?>
                                <?php foreach ($recentEvents as $event): ?>
                                    <li class="flex items-center justify-between rounded-2xl px-4 py-3 bg-white/5 border border-white/10">
                                        <div>
                                            <p class="font-semibold text-white"><?php echo h($event['judul']); ?></p>
                                            <p class="text-xs text-white/50">Tanggal: <?php echo h($event['tanggal']); ?></p>
                                        </div>
                                        <a href="event/index.php?edit_event=<?php echo (int)$event['id_event']; ?>" class="text-sm font-semibold text-tide hover:underline">Edit</a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="text-sm text-white/50">Belum ada event yang tercatat.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="glass-panel rounded-[2rem] p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-white/50">Personel</p>
                                <h2 class="text-xl font-semibold text-white">Member Terbaru</h2>
                            </div>
                            <a href="member/index.php" class="text-sm font-semibold text-blush hover:underline">Kelola member</a>
                        </div>
                        <ul class="space-y-3">
                            <?php if ($recentMembers): ?>
                                <?php foreach ($recentMembers as $member): ?>
                                    <li class="flex items-center justify-between rounded-2xl px-4 py-3 bg-white/5 border border-white/10">
                                        <div>
                                            <p class="font-semibold text-white"><?php echo h($member['nama']); ?></p>
                                            <p class="text-xs text-white/50"><?php echo h($member['nama_divisi'] ?? 'Belum ada divisi'); ?></p>
                                        </div>
                                        <a href="member/index.php?edit_member=<?php echo (int)$member['id_member']; ?>" class="text-sm font-semibold text-blush hover:underline">Detail</a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="text-sm text-white/50">Belum ada member yang tercatat.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </section>

                <section class="glass-panel rounded-[2rem] p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-white/50">Struktur</p>
                            <h2 class="text-xl font-semibold text-white">Divisi Aktif</h2>
                        </div>
                        <a href="divisi/index.php" class="text-sm font-semibold text-tide hover:underline">Kelola divisi</a>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <?php if ($recentDivisi): ?>
                            <?php foreach ($recentDivisi as $divisi): ?>
                                <article class="rounded-2xl px-4 py-3 flex items-center justify-between bg-white/5 border border-white/10">
                                    <span class="font-semibold text-white"><?php echo h($divisi['nama_divisi']); ?></span>
                                    <a href="divisi/index.php?edit_divisi=<?php echo (int)$divisi['id_divisi']; ?>" class="text-sm font-semibold text-tide hover:underline">Edit</a>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-white/50">Belum ada data divisi.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
