import re

file_path = r'c:\xampp\htdocs\Web_SEMA\index.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Tailwind config colors
content = content.replace('night: "#050505"', 'night: "#050a14"') # Very dark blue
# midnight is already "#140a05" from previous update (Very dark orange)
content = content.replace('accent: "#F97316"', 'accent: "#0FD7D6"') # Revert accent to Cyan/Blue

# Restore the nav-link underline color to Cyan (if it was changed to #F97316)
content = content.replace('background: #F97316;', 'background: #0FD7D6;')
content = content.replace('background-color: #F97316', 'background-color: #0FD7D6')

# 2. Mix up the tags in the sections to alternate colors
content = content.replace('<span class="text-xs uppercase tracking-[0.5em] text-white/60">Tentang SEMA</span>',
                          '<span class="text-xs uppercase tracking-[0.5em] text-orange-500">Tentang SEMA</span>')

content = content.replace('<span class="text-xs uppercase tracking-[0.5em] text-white/60">Bidang Senat Mahasiswa</span>',
                          '<span class="text-xs uppercase tracking-[0.5em] text-accent">Bidang Senat Mahasiswa</span>')

content = content.replace('<span class="text-xs uppercase tracking-[0.5em] text-white/60">Unit Kegiatan Mahasiswa</span>',
                          '<span class="text-xs uppercase tracking-[0.5em] text-orange-500">Unit Kegiatan Mahasiswa</span>')

content = content.replace('<span class="text-xs uppercase tracking-[0.5em] text-white/60">Berita Terkini</span>',
                          '<span class="text-xs uppercase tracking-[0.5em] text-accent">Berita Terkini</span>')

content = content.replace('<span class="text-xs uppercase tracking-[0.5em] text-white/60">Kontak Kami</span>',
                          '<span class="text-xs uppercase tracking-[0.5em] text-orange-500">Kontak Kami</span>')

# 3. Mix buttons in Hero section
# "TENTANG KAMI" to be Orange, "HUBUNGI KAMI" to be Blue
content = content.replace('bg-accent text-night font-bold rounded-lg', 'bg-orange-500 text-white font-bold rounded-lg')
content = content.replace('border border-white/20 text-white font-bold', 'border border-accent text-accent font-bold')
content = content.replace('hover:border-white/50 hover:bg-white/5', 'hover:bg-accent/10 hover:text-white')

# 4. In Tentang SEMA, we have 4 tags: Solidaritas, Potensi, Sosial, Kreativitas.
# Let's make half of them blue, half orange.
old_tags = """<span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Solidaritas</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Potensi</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Sosial</span>
                            <span class="px-4 py-2 clean-panel rounded-lg text-xs uppercase tracking-[0.35em] text-white/70">Kreativitas</span>"""
new_tags = """<span class="px-4 py-2 bg-accent/10 text-accent border border-accent/20 rounded-lg text-xs uppercase tracking-[0.35em]">Solidaritas</span>
                            <span class="px-4 py-2 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-lg text-xs uppercase tracking-[0.35em]">Potensi</span>
                            <span class="px-4 py-2 bg-accent/10 text-accent border border-accent/20 rounded-lg text-xs uppercase tracking-[0.35em]">Sosial</span>
                            <span class="px-4 py-2 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-lg text-xs uppercase tracking-[0.35em]">Kreativitas</span>"""
content = content.replace(old_tags, new_tags)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Mix completed.")
