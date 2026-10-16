/**
 * Client HTTP generic terpusat berbasis fetch API.
 * Bertanggung jawab menangani detail teknis HTTP, CSRF token, query string, dan error handling.
 */

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getApiToken() {
    return localStorage.getItem('api_token') || sessionStorage.getItem('api_token') || '';
}

function buildHeaders(customHeaders = {}) {
    const headers = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
        ...customHeaders
    };

    const token = getApiToken();
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    return headers;
}

function buildQueryString(params = {}) {
    if (!params || typeof params !== 'object') return '';

    const query = new URLSearchParams();
    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            query.append(key, value);
        }
    });

    const queryString = query.toString();
    return queryString ? `?${queryString}` : '';
}

async function handleResponse(response) {
    let payload;
    const contentType = response.headers.get('content-type') || '';

    if (contentType.includes('application/json')) {
        payload = await response.json();
    } else {
        payload = await response.text();
    }

    if (!response.ok) {
        const error = new Error(payload?.message || `HTTP Error ${response.status}`);
        error.status = response.status;
        error.payload = payload;
        throw error;
    }

    return payload;
}

export const apiClient = {
    async get(url, params = {}, options = {}) {
        const fullUrl = `${url}${buildQueryString(params)}`;
        const response = await fetch(fullUrl, {
            method: 'GET',
            headers: buildHeaders(options.headers),
            credentials: 'same-origin',
            ...options
        });
        return handleResponse(response);
    },

    async post(url, data = {}, options = {}) {
        const isFormData = data instanceof FormData;
        const headers = buildHeaders(options.headers);

        if (!isFormData && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, {
            method: 'POST',
            headers,
            body: isFormData ? data : JSON.stringify(data),
            credentials: 'same-origin',
            ...options
        });
        return handleResponse(response);
    },

    async put(url, data = {}, options = {}) {
        const headers = buildHeaders(options.headers);
        if (!headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, {
            method: 'PUT',
            headers,
            body: JSON.stringify(data),
            credentials: 'same-origin',
            ...options
        });
        return handleResponse(response);
    },

    async del(url, options = {}) {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: buildHeaders(options.headers),
            credentials: 'same-origin',
            ...options
        });
        return handleResponse(response);
    }
};
