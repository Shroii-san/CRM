/**
 * Mengekstrak nilai bertingkat (nested property) dari objek secara aman.
 * Contoh: getNestedValue(row, 'industry.name', '-')
 *
 * @param {Object} obj Objek sumber
 * @param {string} path Path properti dipisahkan oleh titik (misal: 'user.profile.name')
 * @param {*} defaultValue Nilai default jika properti tidak ditemukan
 * @returns {*} Nilai properti atau nilai default
 */
export function getNestedValue(obj, path, defaultValue = "-") {
    if (!obj || typeof obj !== "object") return defaultValue;
    if (!path) return defaultValue;

    const keys = path.split(".");
    let current = obj;

    for (const key of keys) {
        if (current === null || current === undefined || !(key in current)) {
            return defaultValue;
        }
        current = current[key];
    }

    return current ?? defaultValue;
}
