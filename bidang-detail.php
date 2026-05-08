<?php
require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php#bidang');
    exit;
}

// Ambil data divisi
$stmt = mysqli_prepare($conn, 'SELECT * FROM divisi WHERE id_divisi = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$divisi = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$divisi) {
    header('Location: index.php#bidang');
    exit;
}

// Ambil program kerja
$prokerResult = mysqli_query($conn, "SELECT * FROM proker WHERE id_divisi = $id ORDER BY id_proker ASC");
$prokerList = $prokerResult ? mysqli_fetch_all($prokerResult, MYSQLI_ASSOC) : [];

// Ambil anggota berdasarkan divisi
$memberResult = mysqli_query($conn, "SELECT * FROM member WHERE id_divisi = $id ORDER BY nama ASC");
$memberList = $memberResult ? mysqli_fetch_all($memberResult, MYSQLI_ASSOC) : [];

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Warna per divisi - unik per bidang
$colorMap = [
    'PENGURUS HARIAN'      => ['accent' => '#0FD7D6', 'bg' => 'bg-teal-400/10',   'text' => 'text-teal-400',   'icon' => 'fa-crown',              'hex' => '#0FD7D6'],
    'MINAT BAKAT'          => ['accent' => '#EC4899', 'bg' => 'bg-pink-400/10',   'text' => 'text-pink-400',   'icon' => 'fa-star',               'hex' => '#EC4899'],
    'SOSIAL MASYARAKAT'    => ['accent' => '#10B981', 'bg' => 'bg-emerald-400/10','text' => 'text-emerald-400','icon' => 'fa-hand-holding-heart',  'hex' => '#10B981'],
    'USAHA DANA'           => ['accent' => '#F59E0B', 'bg' => 'bg-amber-400/10',  'text' => 'text-amber-400',  'icon' => 'fa-coins',              'hex' => '#F59E0B'],
    'KOMUNIKASI INFORMASI' => ['accent' => '#8B5CF6', 'bg' => 'bg-purple-400/10', 'text' => 'text-purple-400', 'icon' => 'fa-broadcast-tower',    'hex' => '#8B5CF6'],
];
$namaUpper = strtoupper(trim($divisi['nama_divisi']));
$color = $colorMap[$namaUpper] ?? ['accent' => '#0FD7D6', 'bg' => 'bg-accent/10', 'text' => 'text-accent', 'icon' => 'fa-users'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($divisi['nama_divisi']); ?> — SEMA FTI UAJY</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        night: "#050505",
                        midnight: "#0B0B0F",
                        accent: "#0FD7D6",
                    },
                    fontFamily: {
                        body: ['"Space Grotesk"', "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        :root { --bidang-accent: <?php echo $color['hex'] ?? '#0FD7D6'; ?>; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: var(--bidang-accent); border-radius: 5px; }
        html { scroll-behavior: smooth; }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 30px -10px color-mix(in srgb, var(--bidang-accent) 30%, transparent); }
        .member-photo { transition: transform 0.3s ease; }
        .member-photo:hover { transform: scale(1.05); }
        .clean-panel { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glass-panel { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; }
        /* Proker Table */
        .proker-table th { background: color-mix(in srgb, var(--bidang-accent) 10%, transparent); }
        .proker-table tr:hover td { background: rgba(255,255,255,0.03); }
        .badge-terlaksana   { background: rgba(16,185,129,0.15); color: #10B981; }
        .badge-berjalan     { background: rgba(245,158,11,0.15);  color: #F59E0B; }
        .badge-belum        { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.4); }
    </style>
</head>
<body class="bg-night text-white font-body antialiased">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-night/70 backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex items-center justify-between h-20">
                <a href="index.php" class="flex items-center gap-3 group">
                    <img src="asset/img/icon.png" alt="Logo SEMA FTI UAJY"
                         class="h-12 w-12 rounded-full border border-white/10 bg-white/5 p-1 object-contain transition-transform group-hover:scale-105">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-white/60">FTI UAJY</p>
                        <p class="font-display text-xl tracking-wide text-white">SENAT MAHASISWA</p>
                    </div>
                </a>
                <button onclick="history.back()" class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-ember/90 to-tide/90 text-night font-semibold shadow-[0_10px_30px_-15px_rgba(15,215,214,0.6)] hover:shadow-[0_0_30px_rgba(241,90,41,0.35)] transition-all">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>
    </header>

    <main class="pt-20">

        <!-- Hero Bidang -->
        <section class="relative py-20 lg:py-28 overflow-hidden">
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <div class="absolute right-0 top-0 h-96 w-96 rounded-full blur-3xl" style="background: <?php echo $color['accent']; ?>"></div>
                <div class="absolute left-0 bottom-0 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>
            </div>
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex items-center gap-4 mb-6">
                    <a href="index.php#bidang" class="text-white/40 hover:text-white transition-colors text-sm flex items-center gap-1">
                        <i class="fas fa-home text-xs"></i> Home
                    </a>
                    <i class="fas fa-chevron-right text-white/20 text-xs"></i>
                    <span class="text-white/60 text-sm">Bidang</span>
                    <i class="fas fa-chevron-right text-white/20 text-xs"></i>
                    <span class="text-sm <?php echo $color['text']; ?>"><?php echo h($divisi['nama_divisi']); ?></span>
                </div>

                <div class="grid lg:grid-cols-[auto_1fr] gap-6 items-start">
                    <div class="w-20 h-20 <?php echo $color['bg']; ?> rounded-3xl flex items-center justify-center flex-shrink-0 glass-panel">
                        <i class="fas <?php echo $color['icon']; ?> <?php echo $color['text']; ?> text-3xl"></i>
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] <?php echo $color['text']; ?>">Bidang SEMA FTI UAJY</span>
                        <h1 class="font-display text-4xl lg:text-5xl font-bold mt-2 mb-4"><span class="text-gradient"><?php echo h($divisi['nama_divisi']); ?></span></h1>
                        <p class="text-white/60 max-w-2xl">Kami mengelola program kerja strategis dan kolaboratif untuk menjaga ritme aspirasi dan karya di SEMA FTI UAJY.</p>
                        <?php if (!empty($divisi['nama_koor'])): ?>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 <?php echo $color['bg']; ?> rounded-full flex items-center justify-center">
                                <?php if (!empty($divisi['foto_koor'])): ?>
                                    <img src="<?php echo h($divisi['foto_koor']); ?>" class="w-full h-full object-cover rounded-full">
                                <?php else: ?>
                                    <i class="fas fa-user <?php echo $color['text']; ?> text-sm"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="text-white font-semibold"><?php echo h($divisi['nama_koor']); ?></p>
                                <p class="text-white/50 text-xs uppercase tracking-wider">Koordinator Bidang</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visi Misi -->
        <?php if (!empty($divisi['visi']) || !empty($divisi['misi'])): ?>
        <section class="py-12 border-t border-white/5">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid md:grid-cols-2 gap-8">
                    <?php if (!empty($divisi['visi'])): ?>
                    <div class="glass-panel rounded-[2rem] p-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 <?php echo $color['bg']; ?> rounded-xl flex items-center justify-center">
                                <i class="fas fa-eye <?php echo $color['text']; ?>"></i>
                            </div>
                            <span class="text-xs uppercase tracking-[0.5em] text-white/60">Visi</span>
                        </div>
                        <p class="text-white/80 text-lg leading-relaxed"><?php echo h($divisi['visi']); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($divisi['misi'])): ?>
                    <div class="glass-panel rounded-[2rem] p-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 <?php echo $color['bg']; ?> rounded-xl flex items-center justify-center">
                                <i class="fas fa-bullseye <?php echo $color['text']; ?>"></i>
                            </div>
                            <span class="text-xs uppercase tracking-[0.5em] text-white/60">Misi</span>
                        </div>
                        <p class="text-white/80 text-lg leading-relaxed"><?php echo h($divisi['misi']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Program Kerja -->
        <section class="py-12 border-t border-white/5">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] text-white/60">Program Kerja</span>
                        <h2 class="font-display text-2xl md:text-3xl font-bold mt-2">Rangkaian Program Bidang</h2>
                    </div>
                    <span class="text-white/40 text-sm"><?php echo count($prokerList); ?> proker</span>
                </div>

                <?php if ($prokerList): ?>
                <div class="overflow-x-auto rounded-2xl border border-white/10">
                    <table class="proker-table w-full text-sm border-collapse">
                        <thead>
                            <tr>
                                <th class="px-5 py-4 text-left text-xs uppercase tracking-widest text-white/50 font-semibold w-12">No</th>
                                <th class="px-5 py-4 text-left text-xs uppercase tracking-widest text-white/50 font-semibold">Nama Program Kerja</th>
                                <th class="px-5 py-4 text-left text-xs uppercase tracking-widest text-white/50 font-semibold hidden md:table-cell">Deskripsi</th>
                                <th class="px-5 py-4 text-left text-xs uppercase tracking-widest text-white/50 font-semibold hidden sm:table-cell w-40">Tanggal</th>
                                <th class="px-5 py-4 text-center text-xs uppercase tracking-widest text-white/50 font-semibold w-36">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($prokerList as $i => $proker): ?>
                            <?php
                                $status = $proker['status'] ?? 'Belum Terlaksana';
                                $badgeClass = match($status) {
                                    'Terlaksana'       => 'badge-terlaksana',
                                    'Berjalan'         => 'badge-berjalan',
                                    default            => 'badge-belum',
                                };
                                $statusIcon = match($status) {
                                    'Terlaksana' => 'fa-circle-check',
                                    'Berjalan'   => 'fa-spinner fa-spin',
                                    default      => 'fa-clock',
                                };
                                $tanggal = !empty($proker['tanggal_pelaksanaan'])
                                    ? date('d M Y', strtotime($proker['tanggal_pelaksanaan']))
                                    : '-';
                            ?>
                            <tr class="transition-colors duration-150" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-5 py-4">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold" style="background: color-mix(in srgb, var(--bidang-accent) 12%, transparent); color: var(--bidang-accent);">
                                        <?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($proker['gambar'])): ?>
                                        <img src="uploads/proker/<?php echo h($proker['gambar']); ?>"
                                             alt="<?php echo h($proker['nama_proker']); ?>"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0 hidden sm:block">
                                        <?php endif; ?>
                                        <span class="font-semibold text-white"><?php echo h($proker['nama_proker']); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-white/55 hidden md:table-cell max-w-xs">
                                    <p class="line-clamp-2 leading-relaxed"><?php echo h($proker['deskripsi'] ?? '-'); ?></p>
                                </td>
                                <td class="px-5 py-4 text-white/50 hidden sm:table-cell whitespace-nowrap">
                                    <?php echo $tanggal; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                        <i class="fas <?php echo $statusIcon; ?> text-xs"></i>
                                        <?php echo h($status); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-16 glass-panel rounded-[2rem]">
                    <i class="fas fa-folder-open text-white/20 text-4xl mb-4 block"></i>
                    <p class="text-white/40">Belum ada program kerja yang terdaftar.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Anggota -->
        <section class="py-12 border-t border-white/5">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <span class="text-xs uppercase tracking-[0.5em] text-white/60">Personel</span>
                        <h2 class="font-display text-2xl md:text-3xl font-bold mt-2">Anggota Bidang</h2>
                    </div>
                    <span class="text-white/40 text-sm"><?php echo count($memberList); ?> anggota</span>
                </div>

                <?php if ($memberList): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <?php foreach ($memberList as $member): ?>
                    <div class="text-center group glass-panel rounded-2xl p-4">
                        <div class="member-photo relative w-full aspect-square rounded-2xl overflow-hidden border border-white/10 mb-3">
                            <?php if (!empty($member['foto_member'])): ?>
                                <img src="<?php echo h($member['foto_member']); ?>"
                                     alt="<?php echo h($member['nama']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full <?php echo $color['bg']; ?> flex items-center justify-center">
                                    <span class="<?php echo $color['text']; ?> text-2xl font-bold">
                                        <?php echo strtoupper(substr($member['nama'], 0, 1)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <p class="font-semibold text-white text-sm leading-tight"><?php echo h($member['nama']); ?></p>
                        <p class="text-white/40 text-xs mt-1">Anggota</p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-16 glass-panel rounded-[2rem]">
                    <i class="fas fa-users text-white/20 text-4xl mb-4 block"></i>
                    <p class="text-white/40">Belum ada anggota yang terdaftar di bidang ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tombol Kembali -->
        <section class="py-12 border-t border-white/5">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <a href="index.php#bidang" class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-gradient-to-r from-ember to-tide text-night font-semibold shadow-[0_10px_30px_-15px_rgba(15,215,214,0.6)] hover:shadow-[0_0_30px_rgba(241,90,41,0.35)] transition-all">
                    <i class="fas fa-arrow-left"></i> Kembali ke Semua Bidang
                </a>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-night/90 py-8">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <p class="text-white/40 text-sm">© 2025 Senat Mahasiswa FTI UAJY. Developed by KOMINFO SEMA FTI UAJY</p>
        </div>
    </footer>

</body>
</html>