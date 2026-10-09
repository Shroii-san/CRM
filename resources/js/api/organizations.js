import { apiClient } from './apiClient.js';

const BASE_URL = '/api/v1/organizations';

/**
 * Modul API khusus untuk resource Organization.
 * Menerjemahkan kebutuhan halaman ke pemanggilan API terpusat melalui apiClient.
 */

export function getOrganizations(params = {}) {
    return apiClient.get(BASE_URL, params);
}

export function getOrganization(id) {
    return apiClient.get(`/organization/${id}`);
}

export function getOrganizationContacts(id) {
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
