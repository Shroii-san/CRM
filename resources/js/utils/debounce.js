/**
 * Menunda eksekusi fungsi hingga durasi tertentu berlalu sejak pemanggilan terakhir.
 * @param {Function} fn Fungsi yang akan dieksekusi
 * @param {number} wait Waktu tunda dalam milidetik (default: 300)
 * @returns {Function} Fungsi yang ter-debounce
 */
export function debounce(fn, wait = 300) {
    let timeoutId = null;

    return function (...args) {
        const context = this;
        if (timeoutId !== null) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            fn.apply(context, args);
        }, wait);
    };
}
