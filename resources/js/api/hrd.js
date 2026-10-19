import { apiClient } from './apiClient.js';

const BASE_URL = '/api/v1/hrd';

/**
 * Modul API khusus untuk resource HRD / Candidate Management.
 * Menerjemahkan kebutuhan halaman ke pemanggilan API terpusat melalui apiClient.
 */

export function getCandidates(params = {}) {
    return apiClient.get(BASE_URL, params);
}

export function getCandidate(id) {
    return apiClient.get(`${BASE_URL}/${id}`);
}

export function createCandidate(data) {
    return apiClient.post(BASE_URL, data);
}

export function updateCandidate(id, data) {
    return apiClient.put(`${BASE_URL}/${id}`, data);
}

export function deleteCandidate(id) {
    return apiClient.del(`${BASE_URL}/${id}`);
}

export function promoteCandidate(id, data = {}) {
    return apiClient.post(`${BASE_URL}/${id}/promote`, data);
}

