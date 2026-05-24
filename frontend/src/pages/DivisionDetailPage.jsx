import { useEffect, useMemo, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import { ArrowLeft, BookOpen, Users, FileText, ChevronRight, User } from "lucide-react";
import Badge from "../components/ui/Badge.jsx";
import Card from "../components/ui/Card.jsx";
import Skeleton from "../components/ui/Skeleton.jsx";
import { fetchDivisiById } from "../services/divisiService.js";
import { fetchProkerList } from "../services/prokerService.js";
import { fetchMemberList } from "../services/memberService.js";
import { assetUrl } from "../utils/asset.js";

const statusMap = {
  Terlaksana: "success",
  Berjalan: "warning",
  "Belum Terlaksana": "neutral",
};

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.08 } },
};

const itemVariants = {
  hidden: { opacity: 0, y: 20 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.5, ease: [0.25, 0.1, 0.25, 1] },
  },
};

export default function DivisionDetailPage() {
  const { id } = useParams();
  const [division, setDivision] = useState(null);
  const [prokers, setProkers] = useState([]);
  const [members, setMembers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const run = async () => {
      const [divRes, prokerRes, memberRes] = await Promise.all([
        fetchDivisiById(id),
        fetchProkerList(id),
        fetchMemberList(id),
      ]);

      if (divRes.success) setDivision(divRes.data);
      if (prokerRes.success) setProkers(prokerRes.data || []);
      if (memberRes.success) setMembers(memberRes.data || []);
      setLoading(false);
    };

    run();
  }, [id]);

  const subtitle = useMemo(() => {
    if (!division?.nama_divisi) return "";
    return division.nama_divisi
      .toLowerCase()
      .replace(/\b\w/g, (c) => c.toUpperCase());
  }, [division]);

  if (loading) {
    return (
      <div className="mx-auto max-w-6xl px-6 py-16">
        <Skeleton className="h-12 w-2/3" />
        <Skeleton className="mt-6 h-40" />
      </div>
    );
  }

  if (!division) {
    return (
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        className="mx-auto max-w-6xl px-6 py-16 text-center"
      >
        <p className="text-white/70 text-lg">Data bidang tidak ditemukan.</p>
        <Link
          to="/"
          className="mt-4 inline-flex items-center gap-2 text-secondary hover:text-white transition-colors"
        >
          <ArrowLeft size={16} />
          Kembali ke beranda
        </Link>
      </motion.div>
    );
  }

  return (
    <div className="bg-night">
      {/* Hero header */}
      <section className="relative overflow-hidden border-b border-white/10 py-20">
        {/* Decorative orbs */}
        <div className="absolute -top-32 -right-32 w-96 h-96 bg-secondary/8 rounded-full blur-[120px] pointer-events-none" />
        <div className="absolute -bottom-32 -left-32 w-96 h-96 bg-accent2/8 rounded-full blur-[120px] pointer-events-none" />

        <div className="mx-auto max-w-6xl px-6 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <Link
              to="/"
              className="inline-flex items-center gap-2 text-sm text-white/40 hover:text-secondary transition-colors mb-6"
            >
              <ArrowLeft size={14} />
              <span>Kembali</span>
            </Link>

            <motion.p
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-secondary font-semibold"
            >
              <span className="w-8 h-[1px] bg-secondary/50" />
              Bidang
            </motion.p>

            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2, duration: 0.7 }}
              className="mt-4 text-3xl font-bold md:text-5xl lg:text-6xl"
            >
              <span className="gradient-text-orange-blue">{subtitle}</span>
            </motion.h1>

            <motion.p
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="mt-5 max-w-2xl text-white/50 leading-relaxed"
            >
              {division.visi ||
                "Bidang ini menjalankan program strategis untuk mendukung visi dan misi SEMA FTI UAJY."}
            </motion.p>

            {/* Quick stats */}
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="mt-8 flex flex-wrap gap-4"
            >
              <div className="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5">
                <FileText size={14} className="text-secondary" />
                <span className="text-sm text-white/60">{prokers.length} Program Kerja</span>
              </div>
              <div className="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5">
                <Users size={14} className="text-accent2" />
                <span className="text-sm text-white/60">{members.length} Anggota</span>
              </div>
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Visi & Misi */}
      <section className="py-16">
        <div className="mx-auto grid max-w-6xl gap-6 px-6 lg:grid-cols-2">
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            whileHover={{ y: -4 }}
            className="group relative rounded-2xl border border-white/10 bg-white/[0.03] p-7 overflow-hidden transition-all duration-500 hover:border-accent2/30"
          >
            <div className="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-accent2 to-blue-300 rounded-l-2xl" />
            <div className="flex items-center gap-3 mb-4">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-accent2 to-blue-300 flex items-center justify-center">
                <BookOpen size={18} className="text-white" />
              </div>
              <h2 className="text-lg font-bold text-accent2">Visi</h2>
            </div>
            <p className="text-sm text-white/55 leading-relaxed">{division.visi || "-"}</p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            whileHover={{ y: -4 }}
            className="group relative rounded-2xl border border-white/10 bg-white/[0.03] p-7 overflow-hidden transition-all duration-500 hover:border-secondary/30"
          >
            <div className="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-secondary to-orange-300 rounded-l-2xl" />
            <div className="flex items-center gap-3 mb-4">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-secondary to-orange-300 flex items-center justify-center">
                <BookOpen size={18} className="text-white" />
              </div>
              <h2 className="text-lg font-bold text-secondary">Misi</h2>
            </div>
            <p className="text-sm text-white/55 leading-relaxed">{division.misi || "-"}</p>
          </motion.div>
        </div>
      </section>

      {/* Program Kerja */}
      <section className="relative bg-midnight py-16 overflow-hidden">
        <div className="absolute top-0 left-0 right-0 section-divider" />
        <div className="absolute -right-40 top-1/2 w-80 h-80 bg-secondary/5 rounded-full blur-[120px] pointer-events-none" />

        <div className="mx-auto max-w-6xl px-6 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="flex items-center justify-between"
          >
            <div className="flex items-center gap-3">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-secondary to-accent2 flex items-center justify-center">
                <FileText size={18} className="text-white" />
              </div>
              <h2 className="text-2xl font-bold">Program Kerja</h2>
            </div>
            <span className="text-sm text-white/40 bg-white/[0.05] px-3 py-1 rounded-lg border border-white/10">
              {prokers.length} proker
            </span>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-white/[0.02]"
          >
            <table className="min-w-full text-sm">
              <thead className="bg-white/[0.04] text-xs uppercase tracking-widest text-white/35">
                <tr>
                  <th className="px-5 py-4 text-left">Program</th>
                  <th className="px-5 py-4 text-left">Deskripsi</th>
                  <th className="px-5 py-4 text-left">Tanggal</th>
                  <th className="px-5 py-4 text-left">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/[0.06]">
                {prokers.length === 0 ? (
                  <tr>
                    <td
                      colSpan={4}
                      className="px-5 py-10 text-center text-white/40"
                    >
                      <FileText size={24} className="mx-auto mb-2 text-white/20" />
                      Belum ada program kerja.
                    </td>
                  </tr>
                ) : (
                  prokers.map((proker, idx) => (
                    <motion.tr
                      key={proker.id_proker}
                      initial={{ opacity: 0, x: -10 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      viewport={{ once: true }}
                      transition={{ delay: idx * 0.05 }}
                      className="hover:bg-white/[0.03] transition-colors"
                    >
                      <td className="px-5 py-4 font-semibold">
                        {proker.nama_proker}
                      </td>
                      <td className="px-5 py-4 text-white/50">
                        {proker.deskripsi || "-"}
                      </td>
                      <td className="px-5 py-4 text-white/50">
                        {proker.tanggal_pelaksanaan || "-"}
                      </td>
                      <td className="px-5 py-4">
                        <Badge variant={statusMap[proker.status] || "neutral"}>
                          {proker.status || "Belum Terlaksana"}
                        </Badge>
                      </td>
                    </motion.tr>
                  ))
                )}
              </tbody>
            </table>
          </motion.div>
        </div>
      </section>

      {/* Anggota */}
      <section className="py-16">
        <div className="absolute -left-40 top-1/2 w-80 h-80 bg-accent2/5 rounded-full blur-[120px] pointer-events-none" />

        <div className="mx-auto max-w-6xl px-6 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="flex items-center justify-between"
          >
            <div className="flex items-center gap-3">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-accent2 to-blue-300 flex items-center justify-center">
                <Users size={18} className="text-white" />
              </div>
              <h2 className="text-2xl font-bold">Anggota</h2>
            </div>
            <span className="text-sm text-white/40 bg-white/[0.05] px-3 py-1 rounded-lg border border-white/10">
              {members.length} anggota
            </span>
          </motion.div>

          <motion.div
            className="mt-8 grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
            variants={containerVariants}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true }}
          >
            {members.length === 0 ? (
              <motion.div variants={itemVariants} className="col-span-full">
                <div className="flex flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/[0.02] py-12">
                  <Users size={32} className="text-white/15 mb-3" />
                  <p className="text-white/40">Belum ada anggota terdaftar.</p>
                </div>
              </motion.div>
            ) : (
              members.map((member) => (
                <motion.div
                  key={member.id_member}
                  variants={itemVariants}
                  whileHover={{ y: -6, scale: 1.03 }}
                  transition={{ duration: 0.3 }}
                  className="group flex flex-col items-center gap-4 rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 transition-all duration-500 hover:border-accent2/30 hover:bg-white/[0.05]"
                >
                  <div className="relative h-24 w-24 overflow-hidden rounded-2xl border-2 border-white/10 group-hover:border-accent2/40 transition-all duration-300">
                    {member.foto_member ? (
                      <img
                        src={assetUrl(member.foto_member)}
                        alt={member.nama}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                      />
                    ) : (
                      <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary/10 to-accent2/10">
                        <User size={28} className="text-white/30" />
                      </div>
                    )}
                  </div>
                  <div className="text-center">
                    <p className="text-sm font-semibold">{member.nama}</p>
                    <p className="text-xs text-white/40 mt-1">Anggota</p>
                  </div>
                </motion.div>
              ))
            )}
          </motion.div>
        </div>
      </section>
    </div>
  );
}
