import { apiClient } from "./apiClient.js";

const BASE_URL = "/api/v1/clients";

/**
 * Modul API khusus untuk resource Client.
 * Menerjemahkan kebutuhan halaman ke pemanggilan API terpusat melalui apiClient.
 */

export function getClients(params = {}) {
    return apiClient.get(BASE_URL, params);
}

export function getClient(id) {
    return apiClient.get(`${BASE_URL}/${id}`);
}

export function createClient(data) {
    return apiClient.post(BASE_URL, data);
}

export function updateClient(id, data) {
    return apiClient.put(`${BASE_URL}/${id}`, data);
}

export function deleteClient(id) {
    return apiClient.del(`${BASE_URL}/${id}`);
}
