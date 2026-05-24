import { apiRequest } from "./apiClient.js";

export async function fetchProkerList(divisiId) {
  const query = divisiId ? `?divisi_id=${divisiId}` : "";
  return apiRequest({ url: `/proker.php${query}`, method: "get" });
}

export async function createProker(payload) {
  return apiRequest({ url: "/proker.php", method: "post", data: payload });
}

export async function updateProker(id, payload) {
  return apiRequest({
    url: `/proker.php?id=${id}`,
    method: "put",
    data: payload,
  });
}

export async function deleteProker(id) {
  return apiRequest({ url: `/proker.php?id=${id}`, method: "delete" });
}
