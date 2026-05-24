import { apiRequest } from "./apiClient.js";

export async function fetchMemberList(divisiId) {
  const query = divisiId ? `?divisi_id=${divisiId}` : "";
  return apiRequest({ url: `/member.php${query}`, method: "get" });
}

export async function createMember(payload) {
  return apiRequest({ url: "/member.php", method: "post", data: payload });
}

export async function updateMember(id, payload) {
  return apiRequest({
    url: `/member.php?id=${id}`,
    method: "put",
    data: payload,
  });
}

export async function deleteMember(id) {
  return apiRequest({ url: `/member.php?id=${id}`, method: "delete" });
}
