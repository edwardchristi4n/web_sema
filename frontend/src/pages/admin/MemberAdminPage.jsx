import { useEffect, useState } from "react";
import Button from "../../components/ui/Button.jsx";
import Input from "../../components/ui/Input.jsx";
import Modal from "../../components/ui/Modal.jsx";
import Table from "../../components/ui/Table.jsx";
import { useToast } from "../../components/ui/Toast.jsx";
import { fetchDivisiList } from "../../services/divisiService.js";
import {
  fetchMemberList,
  createMember,
  updateMember,
  deleteMember,
} from "../../services/memberService.js";
import { uploadFile } from "../../services/uploadService.js";
import { assetUrl } from "../../utils/asset.js";

const emptyForm = {
  id_member: null,
  nama: "",
  id_divisi: "",
  foto_member: "",
};

export default function MemberAdminPage() {
  const { addToast } = useToast();
  const [members, setMembers] = useState([]);
  const [divisions, setDivisions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [file, setFile] = useState(null);
  const [preview, setPreview] = useState("");

  const loadData = async () => {
    setLoading(true);
    const [memberRes, divRes] = await Promise.all([
      fetchMemberList(),
      fetchDivisiList(),
    ]);
    if (memberRes.success) setMembers(memberRes.data || []);
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

  const openEdit = (member) => {
    setForm({
      id_member: member.id_member,
      nama: member.nama || "",
      id_divisi: member.id_divisi || "",
      foto_member: member.foto_member || "",
    });
    setFile(null);
    setPreview(member.foto_member ? assetUrl(member.foto_member) : "");
    setOpen(true);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    let fotoMember = form.foto_member;
    if (file) {
      const upload = await uploadFile(file, "member");
      if (!upload.success) {
        addToast(upload.message || "Upload gagal.", "error");
        return;
      }
      fotoMember = upload.data?.path || "";
    }

    const payload = {
      nama: form.nama,
      id_divisi: Number(form.id_divisi),
      foto_member: fotoMember,
    };

    const result = form.id_member
      ? await updateMember(form.id_member, payload)
      : await createMember(payload);

    if (result.success) {
      addToast("Member tersimpan.", "success");
      setOpen(false);
      loadData();
    } else {
      addToast(result.message || "Gagal menyimpan.", "error");
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Hapus member ini?")) return;
    const result = await deleteMember(id);
    if (result.success) {
      addToast("Member dihapus.", "success");
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
            Member
          </p>
          <h2 className="text-2xl font-semibold">Kelola Member</h2>
        </div>
        <Button onClick={openCreate}>Tambah Member</Button>
      </div>

      <Table
        headers={["Nama", "Divisi", "Aksi"]}
        rows={members}
        renderRow={(member) => (
          <tr key={member.id_member} className="hover:bg-white/5">
            <td className="px-4 py-3 font-semibold">{member.nama}</td>
            <td className="px-4 py-3 text-white/60">
              {member.nama_divisi || "-"}
            </td>
            <td className="px-4 py-3 text-right">
              <div className="flex justify-end gap-2">
                <Button
                  size="sm"
                  variant="subtle"
                  onClick={() => openEdit(member)}
                >
                  Edit
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  onClick={() => handleDelete(member.id_member)}
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
        title={form.id_member ? "Edit Member" : "Tambah Member"}
        open={open}
        onClose={() => setOpen(false)}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            placeholder="Nama lengkap"
            value={form.nama}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, nama: event.target.value }))
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
          <div>
            <label className="text-xs uppercase tracking-[0.3em] text-white/50">
              Foto Member
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
                    : form.foto_member
                      ? assetUrl(form.foto_member)
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
