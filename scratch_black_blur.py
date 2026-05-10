import fileinput

file_path = r'c:\xampp\htdocs\Web_SEMA\index.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Navbar Blur Visibility
# Change background from 0.85 opacity to 0.4 opacity to make the blur much more visible
content = content.replace("mainHeader.style.background = 'rgba(5,5,5,0.85)';", "mainHeader.style.background = 'rgba(5,5,5,0.4)';")
# We can also add `backdrop-blur-md` directly to the header class, but modifying the JS is enough since the JS handles it on scroll.

# 2. Add Black Background to Sections
content = content.replace('<section id="home" class="relative w-full h-screen flex items-center bg-night overflow-hidden">',
                          '<section id="home" class="relative w-full h-screen flex items-center bg-[#050505] overflow-hidden">')

content = content.replace('<section id="komunitas" class="py-20 bg-midnight">',
                          '<section id="komunitas" class="py-20 bg-[#050505]">')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Update completed.")
