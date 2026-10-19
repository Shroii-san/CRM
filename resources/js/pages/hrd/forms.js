const INP = "w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm";
const JENJANG = ["SD", "SMP", "SMA/SMK", "D3", "S1", "S2", "S3"];

function escapeHtml(value) {
    return String(value ?? "").replace(
        /[&<>'"]/g,
        (char) =>
            ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                "'": "&#039;",
                '"': "&quot;",
            })[char],
    );
}

function normalisasiTanggal(value) {
    const tanggal = String(value || "").trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(tanggal)) return tanggal;

    const cocok = tanggal.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
    if (!cocok) return "";

    const [, hari, bulan, tahun] = cocok;
    const hasil = `${tahun}-${bulan.padStart(2, "0")}-${hari.padStart(2, "0")}`;
    const date = new Date(`${hasil}T00:00:00`);
    return date.getFullYear() === Number(tahun) &&
        date.getMonth() + 1 === Number(bulan) &&
        date.getDate() === Number(hari)
        ? hasil
        : "";
}

function hitungUsia(tanggalLahir) {
    const tanggal = normalisasiTanggal(tanggalLahir);
    if (!tanggal) return "";

    const hariIni = new Date();
    const lahir = new Date(`${tanggal}T00:00:00`);
    let usia = hariIni.getFullYear() - lahir.getFullYear();
    if (
        hariIni.getMonth() < lahir.getMonth() ||
        (hariIni.getMonth() === lahir.getMonth() &&
            hariIni.getDate() < lahir.getDate())
    ) {
        usia -= 1;
    }
    return usia >= 0 ? `${usia} tahun` : "";
}

function tampilanTanggal(value) {
    const tanggal = normalisasiTanggal(value);
    if (!tanggal) return value || "";

    const [tahun, bulan, hari] = tanggal.split("-");
    return `${hari}/${bulan}/${tahun}`;
}

function tambahPendidikan(containerId, values) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;

    const index = parseInt(wrap.dataset.index || "0", 10);
    wrap.dataset.index = index + 1;
    const data = values || {};

    const row = document.createElement("div");
    row.className =
        "pendidikan-row space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-3";
    row.innerHTML = `
        <div class="grid grid-cols-2 gap-2">
            <select name="education_histories[${index}][level]" class="${INP}">
                <option value="">Jenjang</option>
                ${JENJANG.map((jurusan) => `<option value="${jurusan}">${jurusan}</option>`).join("")}
            </select>
            <input type="text" name="education_histories[${index}][school_name]" placeholder="Nama sekolah" class="${INP}">
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input type="text" name="education_histories[${index}][major]" placeholder="Jurusan" class="${INP}">
            <input type="text" name="education_histories[${index}][degree]" placeholder="Gelar (S.Kom)" class="${INP}">
            <input type="text" name="education_histories[${index}][last_school_location]" placeholder="Lokasi" class="${INP}">
        </div>
        <div class="grid grid-cols-3 gap-2">
            <input type="number" name="education_histories[${index}][start_year]" placeholder="Masuk" class="${INP}">
            <input type="number" name="education_histories[${index}][graduation_year]" placeholder="Lulus" class="${INP}">
            <input type="text" name="education_histories[${index}][score]" placeholder="IPK / Nilai" class="${INP}">
        </div>
        <button type="button" onclick="this.closest('.pendidikan-row').remove()" class="text-xs text-red-500 hover:text-red-700">
            <i class="fas fa-trash mr-1"></i> Hapus baris
        </button>
    `;

    const setValue = (field, value) => {
        const input = row.querySelector(
            `[name="education_histories[${index}][${field}]"]`,
        );
        if (input)
            input.value = value === null || value === undefined ? "" : value;
    };

    setValue("level", data.level ?? data.jenjang);
    setValue("school_name", data.school_name ?? data.nama_sekolah);
    setValue("major", data.major ?? data.jurusan);
    setValue("degree", data.degree ?? data.gelar);
    setValue("last_school_location", data.last_school_location ?? data.lokasi);
    setValue("start_year", data.start_year ?? data.tahun_masuk);
    setValue("graduation_year", data.graduation_year ?? data.tahun_lulus);
    setValue("score", data.score ?? data.ipk_atau_nilai);

    wrap.appendChild(row);
}

function kosongkanPendidikan(containerId, withBlankRow = true) {
    const wrap = document.getElementById(containerId);
    if (!wrap) return;

    wrap.innerHTML = "";
    wrap.dataset.index = 0;
    if (withBlankRow) tambahPendidikan(containerId);
}

function populateCandidateEditForm(buttonOrItem) {
    let data;
    if (buttonOrItem instanceof HTMLElement) {
        data = buttonOrItem.dataset;
    } else {
        const item = buttonOrItem || {};
        data = {
            updateUrl: `/hrd/${item.id}`,
            full_name: item.full_name,
            nik: item.nik,
            phone_number: item.phone_number,
            division: item.division,
            application_date: item.application_date,
            npwp: item.npwp,
            no_bpjs: item.no_bpjs,
            status: item.status,
            foto: item.foto || item.foto_url || "",
            ktp: item.scan_ktp || item.scanKtp || item.scan_ktp_url || "",
            cv: item.cv || item.cv_url || "",
            pendidikan: JSON.stringify(
                item.education_histories || item.educationHistories || [],
            ),
            keluarga: JSON.stringify(item.families || []),
        };
    }

    const form = document.getElementById("editForm");
    if (!form) return;

    form.action = data.updateUrl;

    // Reset any previously selected file inputs
    form.querySelectorAll('input[type="file"]').forEach((input) => {
        input.value = "";
    });

    const setValue = (name, value) => {
        const input = form.querySelector(`[name="${name}"]`);
        if (input)
            input.value = value === undefined || value === null ? "" : value;
    };

    setValue("full_name", data.full_name);
    setValue("nik", data.nik);
    setValue("phone_number", data.phone_number);
    setValue("division", data.division);
    setValue("application_date", data.application_date);
    setValue("npwp", data.npwp);
    setValue("no_bpjs", data.no_bpjs);
    setValue("status", data.status);

    const fotoWrap = document.getElementById("editFotoWrap");
    const fotoImg = document.getElementById("editFotoImg");
    if (fotoWrap && fotoImg) {
        if (data.foto) {
            fotoImg.src = data.foto;
            fotoWrap.classList.remove("hidden");
            fotoWrap.classList.add("flex");
        } else {
            fotoImg.src = "";
            fotoWrap.classList.add("hidden");
            fotoWrap.classList.remove("flex");
        }
    }

    const ktpLink = document.getElementById("editKtpLink");
    if (ktpLink) {
        if (data.ktp) {
            ktpLink.href = data.ktp;
            ktpLink.classList.remove("hidden");
        } else {
            ktpLink.classList.add("hidden");
        }
    }

    const cvLink = document.getElementById("editCvLink");
    if (cvLink) {
        if (data.cv) {
            cvLink.href = data.cv;
            cvLink.classList.remove("hidden");
        } else {
            cvLink.classList.add("hidden");
        }
    }

    let education = [];
    try {
        education =
            typeof data.pendidikan === "string"
                ? JSON.parse(data.pendidikan || "[]")
                : data.pendidikan || [];
    } catch (error) {
        education = [];
    }

    kosongkanPendidikan("pendidikanRowsEdit", false);
    if (education.length) {
        education.forEach((item) =>
            tambahPendidikan("pendidikanRowsEdit", item),
        );
    }
}

function initHrdForms() {
    const setup = () => {
        document
            .querySelectorAll('input[type="file"][name="foto"]')
            .forEach((input) => {
                input.addEventListener("change", () => {
                    const file = input.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        input.value = "";
                        alert(
                            "Foto terlalu besar. Ukuran maksimal foto adalah 2 MB.",
                        );
                    }
                });
            });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", setup);
    } else {
        setup();
    }
}

// Helpers di window
window.tambahPendidikan = tambahPendidikan;
window.kosongkanPendidikan = kosongkanPendidikan;
window.populateCandidateEditForm = populateCandidateEditForm;
window.hitungUsia = hitungUsia;
window.normalisasiTanggal = normalisasiTanggal;
window.tampilanTanggal = tampilanTanggal;
window.escapeHtml = escapeHtml;

export {
    initHrdForms,
    tambahPendidikan,
    kosongkanPendidikan,
    populateCandidateEditForm,
};
