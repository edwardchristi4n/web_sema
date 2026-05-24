import { useEffect, useState } from "react";
import Button from "../../components/ui/Button.jsx";
import Input from "../../components/ui/Input.jsx";
import Textarea from "../../components/ui/Textarea.jsx";
import Modal from "../../components/ui/Modal.jsx";
import Table from "../../components/ui/Table.jsx";
import { useToast } from "../../components/ui/Toast.jsx";
import {
  fetchDivisiList,
  createDivisi,
  updateDivisi,
  deleteDivisi,
} from "../../services/divisiService.js";
import { uploadFile } from "../../services/uploadService.js";
import { assetUrl } from "../../utils/asset.js";

const emptyForm = {
  id_divisi: null,
  nama_divisi: "",
  visi: "",
  misi: "",
  nama_koor: "",
  foto_koor: "",
};

export default function DivisiAdminPage() {
  const { addToast } = useToast();
  const [divisions, setDivisions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [file, setFile] = useState(null);
  const [preview, setPreview] = useState("");

  const loadData = async () => {
    setLoading(true);
    const result = await fetchDivisiList();
    if (result.success) {
      setDivisions(result.data || []);
    }
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

  const openEdit = (division) => {
    setForm({
      id_divisi: division.id_divisi,
      nama_divisi: division.nama_divisi || "",
      visi: division.visi || "",
      misi: division.misi || "",
      nama_koor: division.nama_koor || "",
      foto_koor: division.foto_koor || "",
    });
    setFile(null);
    setPreview(division.foto_koor ? assetUrl(division.foto_koor) : "");
    setOpen(true);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    let fotoKoor = form.foto_koor;
    if (file) {
      const upload = await uploadFile(file, "divisi");
      if (!upload.success) {
        addToast(upload.message || "Upload gagal.", "error");
        return;
      }
      fotoKoor = upload.data?.path || "";
    }

    const payload = {
      nama_divisi: form.nama_divisi,
      visi: form.visi,
      misi: form.misi,
      nama_koor: form.nama_koor,
      foto_koor: fotoKoor,
    };

    const result = form.id_divisi
      ? await updateDivisi(form.id_divisi, payload)
      : await createDivisi(payload);

    if (result.success) {
      addToast("Divisi tersimpan.", "success");
      setOpen(false);
      loadData();
    } else {
      addToast(result.message || "Gagal menyimpan.", "error");
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Hapus divisi ini?")) return;
    const result = await deleteDivisi(id);
    if (result.success) {
      addToast("Divisi dihapus.", "success");
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
            Divisi
          </p>
          <h2 className="text-2xl font-semibold">Kelola Divisi</h2>
        </div>
        <Button onClick={openCreate}>Tambah Divisi</Button>
      </div>

      <Table
        headers={["Nama Divisi", "Koordinator", "Aksi"]}
        rows={divisions}
        renderRow={(division) => (
          <tr key={division.id_divisi} className="hover:bg-white/5">
            <td className="px-4 py-3 font-semibold">{division.nama_divisi}</td>
            <td className="px-4 py-3 text-white/60">
              {division.nama_koor || "-"}
            </td>
            <td className="px-4 py-3 text-right">
              <div className="flex justify-end gap-2">
                <Button
                  size="sm"
                  variant="subtle"
                  onClick={() => openEdit(division)}
                >
                  Edit
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  onClick={() => handleDelete(division.id_divisi)}
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
        title={form.id_divisi ? "Edit Divisi" : "Tambah Divisi"}
        open={open}
        onClose={() => setOpen(false)}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            placeholder="Nama divisi"
            value={form.nama_divisi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, nama_divisi: event.target.value }))
            }
            required
          />
          <Input
            placeholder="Nama koordinator"
            value={form.nama_koor}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, nama_koor: event.target.value }))
            }
          />
          <Textarea
            rows={3}
            placeholder="Visi"
            value={form.visi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, visi: event.target.value }))
            }
          />
          <Textarea
            rows={3}
            placeholder="Misi"
            value={form.misi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, misi: event.target.value }))
            }
          />
          <div>
            <label className="text-xs uppercase tracking-[0.3em] text-white/50">
              Foto Koordinator
            </label>
            <Input
              type="file"
              accept="image/*"
              onChange={(event) => {
                const selected = event.target.files?.[0];
                setFile(selected || null);
                setPreview(
                  selected
                    ? URL.createObjectURL(selected)
                    : form.foto_koor
                      ? assetUrl(form.foto_koor)
                      : "",
                );
              }}
            />
            {preview && (
              <img
                src={preview}
                alt="Preview"
                className="mt-3 h-24 w-24 rounded-xl border border-white/10 object-cover"
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
