import { useState, useEffect } from "react";
import { Link, Outlet, useLocation, useNavigate } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import { Menu, X, Mail, MapPin, Phone, ChevronUp } from "lucide-react";
import { assetUrl } from "../utils/asset.js";

const navLinks = [
  { label: "Home", href: "/#home" },
  { label: "Bidang", href: "/#bidang" },
  { label: "Komunitas", href: "/#komunitas" },
  { label: "Berita", href: "/#berita" },
  { label: "Tentang", href: "/#tentang" },
  { label: "Kontak", href: "/#kontak" },
];

function ScrollToTop() {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const onScroll = () => setVisible(window.scrollY > 400);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <AnimatePresence>
      {visible && (
        <motion.button
          initial={{ opacity: 0, scale: 0.8, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.8, y: 20 }}
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.9 }}
          onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
          className="fixed bottom-8 right-8 z-50 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-secondary to-accent2 text-white shadow-lg shadow-secondary/20 transition-shadow hover:shadow-xl hover:shadow-secondary/30"
          aria-label="Scroll to top"
        >
          <ChevronUp size={20} />
        </motion.button>
      )}
    </AnimatePresence>
  );
}

export default function MainLayout() {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    setOpen(false);
  }, [location]);

  useEffect(() => {
    const handleScroll = () => {
      const hash = location.hash.slice(1);
      if (hash) {
        setTimeout(() => {
          const element = document.getElementById(hash);
          if (element) {
            element.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        }, 100);
      }
    };
    handleScroll();
  }, [location.hash]);

  return (
    <div className="min-h-screen bg-night text-white">
      {/* Header */}
      <motion.header
        initial={{ y: -100 }}
        animate={{ y: 0 }}
        transition={{ duration: 0.6, ease: [0.25, 0.1, 0.25, 1] }}
        className={`sticky top-0 z-50 transition-all duration-500 ${
          scrolled
            ? "border-b border-white/10 bg-night/95 backdrop-blur-xl shadow-lg shadow-black/20"
            : "bg-transparent"
        }`}
      >
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-5">
          <Link to="/" className="flex items-center gap-3 group">
            <motion.div
              whileHover={{ rotate: [0, -10, 10, 0] }}
              transition={{ duration: 0.5 }}
              className="relative h-10 w-10 rounded-full border border-white/10 bg-gradient-to-br from-secondary/20 to-accent2/20 p-1 transition-all group-hover:border-secondary/40 group-hover:shadow-md group-hover:shadow-secondary/20"
            >
              <img
                src={assetUrl("/asset/img/icon.png")}
                alt="SEMA"
                className="h-full w-full rounded-full object-contain"
              />
            </motion.div>
            <div className="leading-tight">
              <p className="text-sm font-semibold tracking-wide">
                Senat Mahasiswa
              </p>
              <p className="text-[10px] uppercase tracking-[0.35em] bg-gradient-to-r from-secondary to-accent2 bg-clip-text text-transparent font-semibold">
                FTI UAJY
              </p>
            </div>
          </Link>

          {/* Desktop Nav */}
          <nav className="hidden items-center gap-1 text-sm md:flex">
            {navLinks.map((item, idx) => (
              <motion.button
                key={item.href}
                onClick={() => {
                  const hash = item.href.split("#")[1];
                  if (location.pathname === "/") {
                    window.location.hash = hash;
                  } else {
                    navigate(`/#${hash}`);
                  }
                }}
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.1 + idx * 0.05 }}
                className="animated-underline rounded-lg px-4 py-2 text-white/70 transition-all duration-200 hover:bg-white/5 hover:text-white"
              >
                {item.label}
              </motion.button>
            ))}
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.5 }}
            >
              <Link
                to="/galeri"
                className="ml-2 inline-block rounded-xl border border-accent2/40 px-4 py-2 text-accent2 transition-all duration-300 hover:border-accent2/80 hover:bg-accent2/10 hover:shadow-md hover:shadow-accent2/10"
              >
                Galeri
              </Link>
            </motion.div>
          </nav>

          {/* Mobile Toggle */}
          <motion.button
            whileTap={{ scale: 0.9 }}
            type="button"
            className="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 transition-all hover:bg-white/5 hover:border-secondary/30 md:hidden"
            onClick={() => setOpen((prev) => !prev)}
            aria-label="Toggle menu"
          >
            <AnimatePresence mode="wait">
              {open ? (
                <motion.div
                  key="close"
                  initial={{ rotate: -90, opacity: 0 }}
                  animate={{ rotate: 0, opacity: 1 }}
                  exit={{ rotate: 90, opacity: 0 }}
                  transition={{ duration: 0.2 }}
                >
                  <X size={18} />
                </motion.div>
              ) : (
                <motion.div
                  key="menu"
                  initial={{ rotate: 90, opacity: 0 }}
                  animate={{ rotate: 0, opacity: 1 }}
                  exit={{ rotate: -90, opacity: 0 }}
                  transition={{ duration: 0.2 }}
                >
                  <Menu size={18} />
                </motion.div>
              )}
            </AnimatePresence>
          </motion.button>
        </div>

        {/* Mobile Menu */}
        <AnimatePresence>
          {open && (
            <motion.div
              initial={{ height: 0, opacity: 0 }}
              animate={{ height: "auto", opacity: 1 }}
              exit={{ height: 0, opacity: 0 }}
              transition={{ duration: 0.3, ease: "easeInOut" }}
              className="overflow-hidden border-t border-white/10 bg-night/98 backdrop-blur-xl md:hidden"
            >
              <div className="flex flex-col gap-1 px-5 py-4">
                {navLinks.map((item, idx) => (
                  <motion.button
                    key={item.href}
                    onClick={() => {
                      const hash = item.href.split("#")[1];
                      setOpen(false);
                      if (location.pathname === "/") {
                        window.location.hash = hash;
                      } else {
                        navigate(`/#${hash}`);
                      }
                    }}
                    initial={{ opacity: 0, x: -20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: idx * 0.05 }}
                    className="rounded-xl px-4 py-3 text-white/70 hover:bg-white/5 hover:text-white transition-all text-left"
                  >
                    {item.label}
                  </motion.button>
                ))}
                <motion.div
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: navLinks.length * 0.05 }}
                >
                  <Link
                    to="/galeri"
                    className="mt-2 block rounded-xl bg-gradient-to-r from-accent2 to-accent px-4 py-3 text-center text-white font-semibold"
                    onClick={() => setOpen(false)}
                  >
                    Galeri
                  </Link>
                </motion.div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </motion.header>

      <main className="noise bg-mesh-dark">
        <Outlet />
      </main>

      {/* Footer */}
      <footer className="relative overflow-hidden border-t border-white/10 bg-night">
        {/* Decorative gradient */}
        <div className="absolute inset-0 pointer-events-none">
          <div className="absolute -top-40 left-1/4 w-80 h-80 bg-secondary/5 rounded-full blur-[100px]" />
          <div className="absolute -top-40 right-1/4 w-80 h-80 bg-accent2/5 rounded-full blur-[100px]" />
        </div>

        <div className="relative mx-auto max-w-7xl px-6 py-14">
          <div className="grid gap-10 md:grid-cols-3">
            {/* Brand */}
            <div>
              <div className="flex items-center gap-3">
                <div className="h-10 w-10 rounded-full bg-gradient-to-br from-secondary/20 to-accent2/20 border border-white/10 p-1">
                  <img
                    src={assetUrl("/asset/img/icon.png")}
                    alt="SEMA"
                    className="h-full w-full rounded-full object-contain"
                  />
                </div>
                <div>
                  <p className="font-semibold">Senat Mahasiswa</p>
                  <p className="text-[10px] uppercase tracking-[0.35em] bg-gradient-to-r from-secondary to-accent2 bg-clip-text text-transparent font-semibold">
                    FTI UAJY
                  </p>
                </div>
              </div>
              <p className="mt-4 text-sm text-white/50 leading-relaxed">
                Wadah aspirasi dan kreativitas mahasiswa FTI UAJY dengan
                pendekatan kolaboratif dan inovatif.
              </p>
              {/* Social */}
              <div className="mt-5 flex gap-3">
                {[
                  { icon: Mail, label: "Email" },
                  { icon: Mail, label: "Media Sosial" },
                ].map(({ icon: Icon, label }) => (
                  <a
                    key={label}
                    href="#"
                    className="flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white/40 transition-all hover:border-secondary/40 hover:text-secondary hover:bg-secondary/10"
                    aria-label={label}
                  >
                    <Icon size={16} />
                  </a>
                ))}
              </div>
            </div>

            {/* Navigation */}
            <div>
              <p className="text-xs uppercase tracking-[0.3em] text-white/30 font-semibold mb-4">
                Navigasi
              </p>
              <div className="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                {navLinks.map((item) => (
                  <a
                    key={item.href}
                    href={item.href}
                    className="text-white/50 hover:text-secondary transition-colors py-1"
                  >
                    {item.label}
                  </a>
                ))}
              </div>
            </div>

            {/* Admin & Kontak */}
            <div>
              <p className="text-xs uppercase tracking-[0.3em] text-white/30 font-semibold mb-4">
                Akses
              </p>
              <div className="mb-6 pb-6 border-b border-white/10">
                <Link
                  to="/admin/login"
                  className="inline-block rounded-lg bg-gradient-to-r from-secondary to-accent2 px-4 py-2 text-sm text-white font-semibold transition-all hover:shadow-md hover:shadow-secondary/20"
                >
                  Admin Panel
                </Link>
              </div>
              <p className="text-xs uppercase tracking-[0.3em] text-white/30 font-semibold mb-4">
                Kontak
              </p>
              <div className="space-y-3 text-sm">
                <div className="flex items-start gap-3 text-white/50">
                  <MapPin
                    size={16}
                    className="text-secondary/60 mt-0.5 flex-shrink-0"
                  />
                  <span>Gedung Bonaventura Lt. 4, FTI UAJY</span>
                </div>
                <div className="flex items-center gap-3 text-white/50">
                  <Mail size={16} className="text-accent2/60 flex-shrink-0" />
                  <span>sema@fti.uajy.ac.id</span>
                </div>
                <div className="flex items-center gap-3 text-white/50">
                  <Phone
                    size={16}
                    className="text-secondary/60 flex-shrink-0"
                  />
                  <span>+62 812 3456 7890</span>
                </div>
              </div>
            </div>
          </div>

          {/* Bottom bar */}
          <div className="section-divider mt-10 mb-6" />
          <div className="flex flex-col md:flex-row items-center justify-between gap-4">
            <p className="text-xs text-white/30">
              © 2026 Senat Mahasiswa FTI UAJY. All rights reserved.
            </p>
            <p className="text-xs text-white/20 flex items-center gap-1">
              Crafted with <span className="text-secondary">♥</span> by KOMINFO
              SEMA FTI
            </p>
          </div>
        </div>
      </footer>

      <ScrollToTop />
    </div>
  );
}
