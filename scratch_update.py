import os
import re

file_path = r'c:\xampp\htdocs\Web_SEMA\index.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update colors to Black and Orange alternating
content = content.replace('midnight: "#0B0B0F"', 'midnight: "#140a05"')
content = content.replace('accent: "#0FD7D6"', 'accent: "#F97316"')
# Check for any remaining #0FD7D6 in CSS
content = content.replace('#0FD7D6', '#F97316')

# 2. Alternating Backgrounds
content = content.replace('class="py-20 bg-midnight/30"', 'class="py-20 bg-midnight"')
content = content.replace('<section id="bidang" class="py-20 bg-midnight"', '<section id="bidang" class="py-20 bg-night"')
content = content.replace('<section id="komunitas" class="py-20"', '<section id="komunitas" class="py-20 bg-midnight"')
content = content.replace('<section id="berita" class="py-20"', '<section id="berita" class="py-20 bg-night"')
content = content.replace('<section id="kontak" class="py-20"', '<section id="kontak" class="py-20 bg-midnight"')

# 3. Content Update - Tentang
old_tentang_text = """                        <p class="text-white/70 max-w-2xl">
                            Menjadi jembatan antara mahasiswa, program studi, serta jajaran fakultas berarti memupuk rasa percaya. Kami menjaga ritme komunikasi, menyediakan data aspirasi yang rapi, dan menindaklanjuti isu hingga tuntas.
                        </p>"""
new_tentang_text = """                        <p class="text-white/70 max-w-2xl">
                            SEMA FTI UAJY (berdiri sejak 1990) hadir sebagai lembaga kemahasiswaan tingkat fakultas yang bertujuan membantu membina mahasiswa agar bermoral, berintelektual, dan berintegritas. Kami adalah wadah pengembangan potensi non-akademik serta media penyalur aspirasi mahasiswa.
                        </p>"""
content = content.replace(old_tentang_text, new_tentang_text)

old_tags = """                        <div class="mt-8 flex flex-wrap gap-3">
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Aspirasi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Kolaborasi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Komunitas</span>
                        </div>"""
new_tags = """                        <div class="mt-8 flex flex-wrap gap-3">
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Solidaritas</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Potensi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Sosial</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Kreativitas</span>
                        </div>"""
content = content.replace(old_tags, new_tags)

# Update Bidang Content - Sosmas
content = content.replace('Merancang gerakan sosial dan advokasi, memastikan kebijakan kampus berpihak pada mahasiswa.', 'Wadah kepedulian terhadap isu sosial, lingkungan hidup, dan pengabdian masyarakat.')
content = content.replace('<span>Klinik Aspirasi</span>', '<span>Bakti Sosial</span>')
content = content.replace('<span>Aksi Nyata</span>', '<span>Green Action</span>')

# Update Bidang Content - Kominfo
content = content.replace('Mengemas narasi positif, membangun dokumentasi visual, dan menyebarkan informasi akurat.', 'Pusat penyebaran informasi SEMA, menampung aspirasi, dan mengelola media sosial organisasi.')
content = content.replace('<span>Weekly Digest</span>', '<span>Forum Lesehan</span>')
content = content.replace('<span>Desain & Audio</span>', '<span>Media SEMA FTI</span>')

# Update Bidang Content - USDA
content = content.replace('Mengelola sumber pendanaan organisasi untuk mendukung program dan kegiatan SEMA.', 'Bertanggung jawab terhadap penggalangan dana, promosi usaha, dan merchandise organisasi.')
content = content.replace('<span>Pengelolaan Dana</span>', '<span>Dapoer SEMA</span>')
content = content.replace('<span>Program Wirausaha</span>', '<span>Korsa & Kaos FTI</span>')

# Update Bidang Content - MIBA
content = content.replace('Memfasilitasi pengembangan potensi melalui kegiatan, kompetisi, dan program kreatif.', 'Menyediakan wadah pengembangan minat dan bakat mahasiswa di bidang seni, musik, olahraga, dan jurnalistik.')
content = content.replace('<span>Festival Seni & Olahraga</span>', '<span>SPARKFEST</span>')
content = content.replace('<span>Showcase Bakat</span>', '<span>Manajemen Komunitas</span>')


# 4. Fix Scrollspy Click Bug
old_scrollspy = """            function updateScrollspy() {
                const scrollY = window.scrollY;"""
new_scrollspy = """            let isScrollingFromClick = false;
            let scrollTimeout;
            
            function updateScrollspy() {
                if (isScrollingFromClick) return;
                const scrollY = window.scrollY;"""
content = content.replace(old_scrollspy, new_scrollspy)

# We use regex to replace the old click handler
click_regex = r"anchor\.addEventListener\('click', function\(e\) \{.*?window\.scrollTo\(\{.*?top: targetPosition,.*?behavior: 'smooth'.*?\}\);.*?\}\);\s*\}\);"
new_click = """anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    
                    if (href === '#') return;
                    
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.getElementById(href.substring(1));
                        
                        if (target) {
                            // Stop scrollspy temporarily
                            isScrollingFromClick = true;
                            
                            // Update active link immediately
                            const navLinks = document.querySelectorAll('.nav-link');
                            navLinks.forEach(link => link.classList.remove('active'));
                            this.classList.add('active');
                            
                            const headerHeight = document.querySelector('header').offsetHeight;
                            const targetPosition = target.offsetTop - headerHeight;
                            
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                            
                            // Resume scrollspy after scroll completes
                            clearTimeout(scrollTimeout);
                            scrollTimeout = setTimeout(() => {
                                isScrollingFromClick = false;
                            }, 800);
                        }
                    }
                });"""
content = re.sub(click_regex, new_click, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Update completed.")
