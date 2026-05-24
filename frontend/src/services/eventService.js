import { apiRequest } from "./apiClient.js";

export async function fetchEventList(limit) {
  const query = limit ? `?limit=${limit}` : "";
  return apiRequest({ url: `/event.php${query}`, method: "get" });
}

export async function createEvent(payload) {
  return apiRequest({ url: "/event.php", method: "post", data: payload });
}

export async function updateEvent(id, payload) {
  return apiRequest({
    url: `/event.php?id=${id}`,
    method: "put",
    data: payload,
  });
}

export async function deleteEvent(id) {
  return apiRequest({ url: `/event.php?id=${id}`, method: "delete" });
}
