import { apiClient } from './apiClient.js';

const BASE_URL = '/api/v1/organizations';

/**
 * Modul API khusus untuk resource Organization.
 * Menerjemahkan kebutuhan halaman ke pemanggilan API terpusat melalui apiClient.
 */

export function getOrganizations(params = {}) {
    return apiClient.get(BASE_URL, params);
}

/**
 * Ambil detail satu organisasi beserta relasi industri, wilayah, contacts, dan social profiles.
 * Menggunakan endpoint REST API yang terstandar.
 */
export function getOrganization(id) {
    return apiClient.get(`${BASE_URL}/${id}`);
}

/**
 * Ambil daftar PIC (contacts) dari satu organisasi.
 * Tetap menggunakan endpoint web /organization/{id}/pics karena format responnya spesifik.
 */
export function getOrganizationPics(id) {
    return apiClient.get(`/organization/${id}/pics`);
}

export function createOrganization(data) {
    return apiClient.post(BASE_URL, data);
}

export function updateOrganization(id, data) {
    return apiClient.put(`${BASE_URL}/${id}`, data);
}

export function deleteOrganization(id) {
    return apiClient.del(`${BASE_URL}/${id}`);
}
