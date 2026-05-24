import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { MapPin, Mail, Phone, Send, CheckCircle, MessageSquare } from "lucide-react";
import Button from "../ui/Button.jsx";
import Input from "../ui/Input.jsx";
import Textarea from "../ui/Textarea.jsx";
import { useToast } from "../ui/Toast.jsx";

const contactInfo = [
  {
    icon: MapPin,
    label: "Lokasi",
    value: "Gedung Bonaventura Lt. 4, Kampus FTI UAJY",
    color: "secondary",
    gradient: "from-secondary to-orange-300",
  },
  {
    icon: Mail,
    label: "Email",
    value: "sema@fti.uajy.ac.id",
    color: "accent2",
    gradient: "from-accent2 to-blue-300",
  },
  {
    icon: Phone,
    label: "Telepon",
    value: "+62 812 3456 7890",
    color: "secondary",
    gradient: "from-secondary to-accent2",
  },
];

export default function ContactSection() {
  const { addToast } = useToast();
  const [form, setForm] = useState({ name: "", email: "", message: "" });
  const [sending, setSending] = useState(false);
  const [sent, setSent] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSending(true);

    // Simulate send
    await new Promise((r) => setTimeout(r, 1000));

    addToast("Terima kasih! Pesanmu sudah terkirim.", "success");
    setForm({ name: "", email: "", message: "" });
    setSending(false);
    setSent(true);
    setTimeout(() => setSent(false), 3000);
  };

  return (
    <section id="kontak" className="relative bg-night py-24 overflow-hidden">
      {/* Decorative */}
      <div className="absolute top-0 left-0 right-0 section-divider" />
      <div className="absolute top-1/2 -translate-y-1/2 left-0 w-[500px] h-[500px] bg-secondary/5 rounded-full blur-[150px] pointer-events-none" />
      <div className="absolute top-1/3 right-0 w-[500px] h-[500px] bg-accent2/5 rounded-full blur-[150px] pointer-events-none" />

      <div className="mx-auto max-w-6xl px-6 relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-100px" }}
          transition={{ duration: 0.7 }}
          className="text-center"
        >
          <motion.p
            initial={{ opacity: 0, y: 10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-accent2 font-semibold"
          >
            <span className="w-8 h-[1px] bg-accent2/50" />
            Kontak
            <span className="w-8 h-[1px] bg-accent2/50" />
          </motion.p>
          <h2 className="mt-5 text-3xl font-bold md:text-5xl">
            Mari <span className="gradient-text-orange-blue">Berkolaborasi</span>
          </h2>
          <p className="mt-5 text-white/50 max-w-lg mx-auto">
            Hubungi kami untuk pertanyaan, ide, atau kolaborasi.
          </p>
        </motion.div>

        <div className="mt-14 grid gap-8 lg:grid-cols-2">
          {/* Contact Info */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true, margin: "-50px" }}
            transition={{ duration: 0.7 }}
            className="space-y-5"
          >
            {contactInfo.map((item, idx) => {
              const IconComponent = item.icon;
              return (
                <motion.div
                  key={item.label}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.1 + idx * 0.1 }}
                  whileHover={{ y: -3, scale: 1.01 }}
                  className="group rounded-2xl border border-white/[0.08] bg-white/[0.02] p-6 transition-all duration-500 hover:border-white/20 hover:bg-white/[0.05] hover:shadow-lg overflow-hidden relative"
                >
                  {/* Hover gradient */}
                  <div className={`absolute inset-0 bg-gradient-to-r ${item.gradient} opacity-0 group-hover:opacity-[0.03] transition-opacity duration-500`} />

                  <div className="relative z-10 flex items-start gap-4">
                    <motion.div
                      whileHover={{ rotate: 12, scale: 1.1 }}
                      className={`flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br ${item.gradient} shadow-lg flex-shrink-0`}
                    >
                      <IconComponent size={20} className="text-white" />
                    </motion.div>
                    <div>
                      <p className="text-[10px] uppercase tracking-[0.3em] text-white/35 font-semibold">
                        {item.label}
                      </p>
                      <p className="mt-1.5 text-sm text-white/70 font-medium">
                        {item.value}
                      </p>
                    </div>
                  </div>
                </motion.div>
              );
            })}

            {/* Decorative quote */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.5 }}
              className="rounded-2xl border border-white/[0.06] bg-gradient-to-br from-secondary/[0.03] to-accent2/[0.03] p-6 flex items-center gap-4"
            >
              <MessageSquare size={24} className="text-secondary/40 flex-shrink-0" />
              <p className="text-sm text-white/40 italic">
                Kami senang mendengar suaramu. Jangan ragu untuk menghubungi kami kapan saja!
              </p>
            </motion.div>
          </motion.div>

          {/* Form */}
          <motion.form
            onSubmit={handleSubmit}
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true, margin: "-50px" }}
            transition={{ duration: 0.7, delay: 0.2 }}
            className="relative space-y-5 rounded-2xl border border-white/10 bg-midnight/80 backdrop-blur-sm p-8 overflow-hidden"
          >
            {/* Form gradient accent */}
            <div className="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-secondary via-accent2 to-secondary" />

            <div>
              <label className="text-xs uppercase tracking-[0.25em] text-white/40 font-semibold mb-2 block">
                Nama Lengkap
              </label>
              <Input
                placeholder="Masukkan nama lengkap"
                value={form.name}
                onChange={(event) =>
                  setForm((prev) => ({ ...prev, name: event.target.value }))
                }
                required
              />
            </div>
            <div>
              <label className="text-xs uppercase tracking-[0.25em] text-white/40 font-semibold mb-2 block">
                Email
              </label>
              <Input
                type="email"
                placeholder="Masukkan email"
                value={form.email}
                onChange={(event) =>
                  setForm((prev) => ({ ...prev, email: event.target.value }))
                }
                required
              />
            </div>
            <div>
              <label className="text-xs uppercase tracking-[0.25em] text-white/40 font-semibold mb-2 block">
                Pesan
              </label>
              <Textarea
                rows={5}
                placeholder="Tuliskan pesan atau ide kolaborasi"
                value={form.message}
                onChange={(event) =>
                  setForm((prev) => ({ ...prev, message: event.target.value }))
                }
                required
              />
            </div>

            <motion.div whileHover={{ scale: 1.01 }} whileTap={{ scale: 0.99 }}>
              <Button
                type="submit"
                variant="gradient-orange-blue"
                size="lg"
                className="w-full"
                disabled={sending}
              >
                <AnimatePresence mode="wait">
                  {sending ? (
                    <motion.span
                      key="sending"
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2"
                    >
                      <motion.div
                        animate={{ rotate: 360 }}
                        transition={{ duration: 1, repeat: Infinity, ease: "linear" }}
                        className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full"
                      />
                      Mengirim...
                    </motion.span>
                  ) : sent ? (
                    <motion.span
                      key="sent"
                      initial={{ opacity: 0, scale: 0.8 }}
                      animate={{ opacity: 1, scale: 1 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2"
                    >
                      <CheckCircle size={18} />
                      Terkirim!
                    </motion.span>
                  ) : (
                    <motion.span
                      key="default"
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      className="flex items-center gap-2"
                    >
                      <Send size={16} />
                      Kirim Pesan
                    </motion.span>
                  )}
                </AnimatePresence>
              </Button>
            </motion.div>
          </motion.form>
        </div>
      </div>
    </section>
  );
}
