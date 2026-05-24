import { apiRequest } from "./apiClient.js";

export async function fetchDivisiList() {
  return apiRequest({ url: "/divisi.php", method: "get" });
}

export async function fetchDivisiById(id) {
  return apiRequest({ url: `/divisi.php?id=${id}`, method: "get" });
}

export async function createDivisi(payload) {
  return apiRequest({ url: "/divisi.php", method: "post", data: payload });
}

export async function updateDivisi(id, payload) {
  return apiRequest({
    url: `/divisi.php?id=${id}`,
    method: "put",
    data: payload,
  });
}

export async function deleteDivisi(id) {
  return apiRequest({ url: `/divisi.php?id=${id}`, method: "delete" });
}
