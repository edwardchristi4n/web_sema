import { useEffect, useState } from "react";
import Button from "../../components/ui/Button.jsx";
import Input from "../../components/ui/Input.jsx";
import Textarea from "../../components/ui/Textarea.jsx";
import Modal from "../../components/ui/Modal.jsx";
import Table from "../../components/ui/Table.jsx";
import { useToast } from "../../components/ui/Toast.jsx";
import {
  fetchEventList,
  createEvent,
  updateEvent,
  deleteEvent,
} from "../../services/eventService.js";
import { uploadFile } from "../../services/uploadService.js";
import { assetUrl } from "../../utils/asset.js";

const emptyForm = {
  id_event: null,
  judul: "",
  deskripsi: "",
  tanggal: "",
  lokasi: "",
  foto_event: "",
};

export default function EventAdminPage() {
  const { addToast } = useToast();
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [file, setFile] = useState(null);
  const [preview, setPreview] = useState("");

  const loadData = async () => {
    setLoading(true);
    const result = await fetchEventList();
    if (result.success) {
      setEvents(result.data || []);
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

  const openEdit = (event) => {
    setForm({
      id_event: event.id_event,
      judul: event.judul || "",
      deskripsi: event.deskripsi || "",
      tanggal: event.tanggal || "",
      lokasi: event.lokasi || "",
      foto_event: event.foto_event || "",
    });
    setFile(null);
    setPreview(event.foto_event ? assetUrl(event.foto_event) : "");
    setOpen(true);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    let fotoEvent = form.foto_event;
    if (file) {
      const upload = await uploadFile(file, "event");
      if (!upload.success) {
        addToast(upload.message || "Upload gagal.", "error");
        return;
      }
      fotoEvent = upload.data?.path || "";
    }

    const payload = {
      judul: form.judul,
      deskripsi: form.deskripsi,
      tanggal: form.tanggal,
      lokasi: form.lokasi,
      foto_event: fotoEvent,
    };

    const result = form.id_event
      ? await updateEvent(form.id_event, payload)
      : await createEvent(payload);

    if (result.success) {
      addToast("Event tersimpan.", "success");
      setOpen(false);
      loadData();
    } else {
      addToast(result.message || "Gagal menyimpan.", "error");
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Hapus event ini?")) return;
    const result = await deleteEvent(id);
    if (result.success) {
      addToast("Event dihapus.", "success");
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
            Event
          </p>
          <h2 className="text-2xl font-semibold">Kelola Event</h2>
        </div>
        <Button onClick={openCreate}>Tambah Event</Button>
      </div>

      <Table
        headers={["Judul", "Tanggal", "Aksi"]}
        rows={events}
        renderRow={(event) => (
          <tr key={event.id_event} className="hover:bg-white/5">
            <td className="px-4 py-3 font-semibold">{event.judul}</td>
            <td className="px-4 py-3 text-white/60">{event.tanggal || "-"}</td>
            <td className="px-4 py-3 text-right">
              <div className="flex justify-end gap-2">
                <Button
                  size="sm"
                  variant="subtle"
                  onClick={() => openEdit(event)}
                >
                  Edit
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  onClick={() => handleDelete(event.id_event)}
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
        title={form.id_event ? "Edit Event" : "Tambah Event"}
        open={open}
        onClose={() => setOpen(false)}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            placeholder="Judul event"
            value={form.judul}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, judul: event.target.value }))
            }
            required
          />
          <Input
            type="date"
            value={form.tanggal}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, tanggal: event.target.value }))
            }
            required
          />
          <Input
            placeholder="Lokasi"
            value={form.lokasi}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, lokasi: event.target.value }))
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
              Foto Event
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
                    : form.foto_event
                      ? assetUrl(form.foto_event)
                      : "",
                );
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
