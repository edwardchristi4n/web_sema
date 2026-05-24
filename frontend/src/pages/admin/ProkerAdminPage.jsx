import { useEffect, useState } from "react";
import Button from "../../components/ui/Button.jsx";
import Input from "../../components/ui/Input.jsx";
import Textarea from "../../components/ui/Textarea.jsx";
import Modal from "../../components/ui/Modal.jsx";
import Table from "../../components/ui/Table.jsx";
import { useToast } from "../../components/ui/Toast.jsx";
import { fetchDivisiList } from "../../services/divisiService.js";
import {
  fetchProkerList,
  createProker,
  updateProker,
  deleteProker,
} from "../../services/prokerService.js";
import { uploadFile } from "../../services/uploadService.js";
import { assetUrl } from "../../utils/asset.js";

const emptyForm = {
  id_proker: null,
  nama_proker: "",
  deskripsi: "",
  id_divisi: "",
  status: "",
  tanggal_pelaksanaan: "",
  gambar: "",
};

export default function ProkerAdminPage() {
  const { addToast } = useToast();
  const [prokers, setProkers] = useState([]);
  const [divisions, setDivisions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [file, setFile] = useState(null);
  const [preview, setPreview] = useState("");

  const loadData = async () => {
    setLoading(true);
    const [prokerRes, divRes] = await Promise.all([
      fetchProkerList(),
      fetchDivisiList(),
    ]);
    if (prokerRes.success) setProkers(prokerRes.data || []);
    if (divRes.success) setDivisions(divRes.data || []);
    setLoading(false);
  };

  useEffect(() => {
    loadData();
  }, []);

  const openCreate = () => {
    setForm(emptyForm);
    setFile(null);
    setPreview("");
    setOpen(true);
  };

  const openEdit = (proker) => {
    setForm({
      id_proker: proker.id_proker,
      nama_proker: proker.nama_proker || "",
      deskripsi: proker.deskripsi || "",
      id_divisi: proker.id_divisi || "",
      status: proker.status || "",
      tanggal_pelaksanaan: proker.tanggal_pelaksanaan || "",
      gambar: proker.gambar || "",
    });
    setFile(null);
    setPreview(
      proker.gambar ? assetUrl(`/uploads/proker/${proker.gambar}`) : "",
    );
    setOpen(true);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    let gambar = form.gambar;
    if (file) {
      const upload = await uploadFile(file, "proker");
      if (!upload.success) {
        addToast(upload.message || "Upload gagal.", "error");
        return;
      }
      gambar = upload.data?.path?.replace("/uploads/proker/", "") || "";
    }

    const payload = {
      nama_proker: form.nama_proker,
      deskripsi: form.deskripsi,
      id_divisi: Number(form.id_divisi),
      status: form.status,
      tanggal_pelaksanaan: form.tanggal_pelaksanaan,
      gambar,
    };

    const result = form.id_proker
      ? await updateProker(form.id_proker, payload)
      : await createProker(payload);

    if (result.success) {
      addToast("Proker tersimpan.", "success");
      setOpen(false);
      loadData();
    } else {
      addToast(result.message || "Gagal menyimpan.", "error");
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Hapus proker ini?")) return;
    const result = await deleteProker(id);
    if (result.success) {
      addToast("Proker dihapus.", "success");
      loadData();
    } else {
      addToast(result.message || "Gagal menghapus.", "error");
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <p className="text-xs uppercase tracking-[0.3em] text-white/50">
            Proker
          </p>
          <h2 className="text-2xl font-semibold">Kelola Program Kerja</h2>
        </div>
        <Button onClick={openCreate}>Tambah Proker</Button>
      </div>

      <Table
        headers={["Program", "Divisi", "Status", "Aksi"]}
        rows={prokers}
        renderRow={(proker) => (
          <tr key={proker.id_proker} className="hover:bg-white/5">
            <td className="px-4 py-3 font-semibold">{proker.nama_proker}</td>
            <td className="px-4 py-3 text-white/60">
              {proker.nama_divisi || "-"}
            </td>
            <td className="px-4 py-3 text-white/60">{proker.status || "-"}</td>
            <td className="px-4 py-3 text-right">
              <div className="flex justify-end gap-2">
                <Button
                  size="sm"
                  variant="subtle"
                  onClick={() => openEdit(proker)}
                >
                  Edit
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  onClick={() => handleDelete(proker.id_proker)}
                >
                  Hapus
                </Button>
              </div>
            </td>
          </tr>
        )}
      />

      {loading && <p className="text-sm text-white/50">Memuat data...</p>}

      <Modal
        title={form.id_proker ? "Edit Proker" : "Tambah Proker"}
        open={open}
        onClose={() => setOpen(false)}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            placeholder="Nama program"
            value={form.nama_proker}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, nama_proker: event.target.value }))
            }
            required
          />
          <select
            className="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white"
            value={form.id_divisi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, id_divisi: event.target.value }))
            }
            required
          >
            <option value="">Pilih divisi</option>
            {divisions.map((division) => (
              <option key={division.id_divisi} value={division.id_divisi}>
                {division.nama_divisi}
              </option>
            ))}
          </select>
          <Input
            placeholder="Status"
            value={form.status}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, status: event.target.value }))
            }
          />
          <Input
            type="date"
            value={form.tanggal_pelaksanaan}
            onChange={(event) =>
              setForm((prev) => ({
                ...prev,
                tanggal_pelaksanaan: event.target.value,
              }))
            }
          />
          <Textarea
            rows={3}
            placeholder="Deskripsi"
            value={form.deskripsi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, deskripsi: event.target.value }))
            }
          />
          <div>
            <label className="text-xs uppercase tracking-[0.3em] text-white/50">
              Gambar Proker
            </label>
            <Input
              type="file"
              accept="image/*"
              onChange={(event) => {
                const selected = event.target.files?.[0];
                setFile(selected || null);
                setPreview(selected ? URL.createObjectURL(selected) : "");
              }}
            />
            {preview && (
              <img
                src={preview}
                alt="Preview"
                className="mt-3 h-28 w-full rounded-xl border border-white/10 object-cover"
              />
            )}
          </div>
          <Button type="submit" className="w-full">
            Simpan
          </Button>
        </form>
      </Modal>
    </div>
  );
}
