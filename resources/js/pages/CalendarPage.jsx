import React, { useState, useEffect } from "react";
import { createPortal } from "react-dom";
import { Calendar, dateFnsLocalizer } from "react-big-calendar";
import { format, parse, startOfWeek, getDay } from "date-fns";
import { enUS, id } from "date-fns/locale";
import withDragAndDrop from "react-big-calendar/lib/addons/dragAndDrop";
import "react-big-calendar/lib/css/react-big-calendar.css";
import "react-big-calendar/lib/addons/dragAndDrop/styles.css";
import axios from "axios";
import { Calendar as CalIcon, Clock, AlignLeft, X, Trash2, Save, Plus } from "lucide-react";

// ============================================
// SETUP AXIOS DENGAN CSRF TOKEN
// ============================================
axios.defaults.headers.common["X-CSRF-TOKEN"] = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";

const DnDCalendar = withDragAndDrop(Calendar);

const locales = {
    "id-ID": id,
    "en-US": enUS,
};

const localizer = dateFnsLocalizer({
    format,
    parse,
    startOfWeek: () => startOfWeek(new Date(), { weekStartsOn: 1 }),
    getDay,
    locales,
});

// ============================================
// STYLES
// ============================================
const styles = {
    container: {
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #f8fafc 0%, #e0f2fe 50%, #ddd6fe 100%)',
        padding: '32px',
    },
    header: {
        marginBottom: '32px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        flexWrap: 'wrap',
        gap: '16px',
    },
    headerLeft: {
        display: 'flex',
        alignItems: 'center',
        gap: '16px',
    },
    iconBox: {
        background: 'linear-gradient(135deg, #3b82f6 0%, #6366f1 100%)',
        padding: '12px',
        borderRadius: '16px',
        boxShadow: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
    },
    title: {
        fontSize: '36px',
        fontWeight: 'bold',
        background: 'linear-gradient(90deg, #2563eb 0%, #4f46e5 100%)',
        WebkitBackgroundClip: 'text',
        WebkitTextFillColor: 'transparent',
        margin: 0,
    },
    subtitle: {
        color: '#475569',
        fontSize: '14px',
        marginTop: '4px',
    },
    addButton: {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        background: 'linear-gradient(90deg, #2563eb 0%, #4f46e5 100%)',
        color: 'white',
        padding: '12px 24px',
        borderRadius: '12px',
        border: 'none',
        fontWeight: '500',
        cursor: 'pointer',
        transition: 'all 0.2s',
    },
    calendarCard: {
        background: 'white',
        borderRadius: '24px',
        boxShadow: '0 20px 25px -5px rgb(0 0 0 / 0.1)',
        padding: '32px',
        border: '1px solid #e2e8f0',
    },
    modalOverlay: {
        position: 'fixed',
        inset: 0,
        background: 'rgba(0, 0, 0, 0.6)',
        backdropFilter: 'blur(4px)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '16px',
        animation: 'fadeIn 0.2s ease-out',
    },
    modalContent: {
        background: 'white',
        borderRadius: '16px',
        boxShadow: '0 25px 50px -12px rgb(0 0 0 / 0.25)',
        width: '100%',
        maxWidth: '600px',
        maxHeight: '90vh',
        overflow: 'hidden',
        animation: 'slideUp 0.3s ease-out',
    },
    modalHeader: {
        background: 'linear-gradient(90deg, #2563eb 0%, #4f46e5 100%)',
        padding: '20px 24px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    modalHeaderLeft: {
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
    },
    modalIconBox: {
        background: 'rgba(255, 255, 255, 0.2)',
        padding: '8px',
        borderRadius: '8px',
    },
    modalTitle: {
        fontSize: '24px',
        fontWeight: 'bold',
        color: 'white',
        margin: 0,
    },
    closeButton: {
        color: 'white',
        background: 'transparent',
        border: 'none',
        padding: '8px',
        borderRadius: '8px',
        cursor: 'pointer',
        transition: 'background 0.2s',
    },
    modalBody: {
        padding: '24px',
        maxHeight: 'calc(90vh - 140px)',
        overflowY: 'auto',
    },

    label: {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        fontSize: '14px',
        fontWeight: '600',
        color: '#334155',
        marginBottom: '8px',
    },
    input: {
        width: '100%',
        border: '2px solid #e2e8f0',
        borderRadius: '12px',
        padding: '12px 16px',
        fontSize: '14px',
        transition: 'all 0.2s',
        outline: 'none',
        color: '#1e293b',
    },
    textarea: {
        width: '100%',
        border: '2px solid #e2e8f0',
        borderRadius: '12px',
        padding: '12px 16px',
        fontSize: '14px',
        transition: 'all 0.2s',
        outline: 'none',
        resize: 'none',
        fontFamily: 'inherit',
        color: '#1e293b',
    },
    checkboxContainer: {
        background: '#f8fafc',
        padding: '16px',
        borderRadius: '12px',
        border: '2px solid #e2e8f0',
    },
    checkboxLabel: {
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        cursor: 'pointer',
    },
    checkbox: {
        width: '20px',
        height: '20px',
        cursor: 'pointer',
    },
    actions: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingTop: '20px',
        marginTop: '20px',
        borderTop: '2px solid #f1f5f9',
    },
    actionsRight: {
        display: 'flex',
        gap: '12px',
    },
    deleteButton: {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        background: '#fef2f2',
        color: '#dc2626',
        border: '2px solid #fecaca',
        padding: '10px 20px',
        borderRadius: '12px',
        fontWeight: '500',
        cursor: 'pointer',
        transition: 'all 0.2s',
    },
    cancelButton: {
        padding: '10px 24px',
        borderRadius: '12px',
        border: '2px solid #cbd5e1',
        background: 'white',
        color: '#475569',
        fontWeight: '500',
        cursor: 'pointer',
        transition: 'all 0.2s',
    },
    saveButton: {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        background: 'linear-gradient(90deg, #2563eb 0%, #4f46e5 100%)',
        color: 'white',
        border: 'none',
        padding: '10px 24px',
        borderRadius: '12px',
        fontWeight: '500',
        cursor: 'pointer',
        transition: 'all 0.2s',
    },
    confirmModal: {
        background: 'white',
        borderRadius: '16px',
        boxShadow: '0 25px 50px -12px rgb(0 0 0 / 0.25)',
        maxWidth: '400px',
        width: '100%',
        overflow: 'hidden',
        animation: 'slideUp 0.3s ease-out',
    },
    confirmHeader: {
        background: 'linear-gradient(90deg, #ef4444 0%, #f43f5e 100%)',
        padding: '16px 24px',
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
    },
    confirmBody: {
        padding: '24px',
    },
    confirmText: {
        color: '#334155',
        lineHeight: '1.6',
        marginBottom: '16px',
    },
    confirmSubtext: {
        fontSize: '14px',
        color: '#64748b',
    },
    confirmActions: {
        background: '#f8fafc',
        padding: '16px 24px',
        display: 'flex',
        justifyContent: 'flex-end',
        gap: '12px',
        borderTop: '1px solid #e2e8f0',
    },
    confirmButton: {
        display: 'flex',
        alignItems: 'center',
        gap: '8px',
        background: 'linear-gradient(90deg, #ef4444 0%, #f43f5e 100%)',
        color: 'white',
        border: 'none',
        padding: '10px 20px',
        borderRadius: '12px',
        fontWeight: '500',
        cursor: 'pointer',
        transition: 'all 0.2s',
    },
};

// ============================================
// COMPONENT MODAL
// ============================================
function Modal({ children, onClose, zIndex = 10000 }) {
    useEffect(() => {
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";
        const onKey = (e) => {
            if (e.key === "Escape") onClose && onClose();
        };
        document.addEventListener("keydown", onKey);
        return () => {
            document.removeEventListener("keydown", onKey);
            document.body.style.overflow = prevOverflow;
        };
    }, [onClose]);

    return createPortal(
        <div
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose && onClose();
            }}
            style={{ ...styles.modalOverlay, zIndex }}
        >
            <div onClick={(e) => e.stopPropagation()}>
                {children}
            </div>
        </div>,
        document.body
    );
}

// ============================================
// COMPONENT UTAMA
// ============================================
export default function CalendarPage() {
    const [events, setEvents] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
    const [selectedEvent, setSelectedEvent] = useState(null);
    const [formData, setFormData] = useState({
        title: "",
        start: "",
        end: "",
        allDay: false,
        description: "",
    });

    useEffect(() => {
        fetchEvents();
    }, []);

    const fetchEvents = async () => {
        try {
            const response = await axios.get("/api/calendar/events");
            const formattedEvents = response.data.map((event) => ({
                ...event,
                start: new Date(event.start),
                end: new Date(event.end),
            }));
            setEvents(formattedEvents);
        } catch (error) {
            console.error("Error fetching events:", error);
            setEvents([]);
        }
    };

    const handleSelectSlot = ({ start, end }) => {
        setSelectedEvent(null);
        setFormData({
            title: "",
            start: start.toISOString().slice(0, 16),
            end: end.toISOString().slice(0, 16),
            allDay: false,
            description: "",
        });
        setShowModal(true);
    };

    const handleSelectEvent = (event) => {
        setSelectedEvent(event);
        setFormData({
            title: event.title,
            start: new Date(event.start).toISOString().slice(0, 16),
            end: new Date(event.end).toISOString().slice(0, 16),
            allDay: event.allDay || false,
            description: event.description || "",
        });
        setShowModal(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();


        try {
            if (selectedEvent) {
                const response = await axios.put(
                    `/api/calendar/events/${selectedEvent.id}`,
                    formData
                );
                setEvents(
                    events.map((ev) =>
                        ev.id === selectedEvent.id
                            ? {
                                  ...response.data,
                                  start: new Date(response.data.start),
                                  end: new Date(response.data.end),
                              }
                            : ev
                    )
                );
                alert("Event berhasil diupdate!");
            } else {
                const response = await axios.post("/api/calendar/events", formData);
                setEvents([
                    ...events,
                    {
                        ...response.data,
                        start: new Date(response.data.start),
                        end: new Date(response.data.end),
                    },
                ]);
                alert("Event berhasil ditambahkan!");
            }

            setShowModal(false);
            resetForm();
        } catch (error) {
            console.error("Error saving event:", error);
            let errorMsg = "Gagal menyimpan event";
            if (error.response?.status === 419) {
                errorMsg = "CSRF Token tidak valid. Refresh halaman dan coba lagi.";
            } else if (error.response?.data?.message) {
                errorMsg = error.response.data.message;
            } else if (error.response?.data?.errors) {
                const errors = Object.values(error.response.data.errors).flat();
                errorMsg = errors.join(", ");
            }
            alert(`ERROR: ${errorMsg}`);
        }
    };

    const handleDeleteClick = () => {
        setShowDeleteConfirm(true);
    };

    const confirmDelete = async () => {
        const idToDelete =
            selectedEvent?.id ??
            selectedEvent?._id ??
            selectedEvent?.eventId ??
            selectedEvent?.uid;

        if (!idToDelete) {
            alert("Error: Event tidak valid (tidak ditemukan id).");
            return;
        }

        try {
            await axios.delete(`/api/calendar/events/${idToDelete}`);

            setEvents((prev) =>
                prev.filter((ev) => {
                    const evId = ev?.id ?? ev?._id ?? ev?.eventId ?? ev?.uid;
                    return evId !== idToDelete;
                })
            );

            alert("Event berhasil dihapus!");
            setShowDeleteConfirm(false);
            setShowModal(false);
            resetForm();
        } catch (error) {
            console.error("DELETE ERROR:", error);
            let errorMsg = "Gagal menghapus event";
            if (error.response?.status === 404) errorMsg = "Event tidak ditemukan";
            else if (error.response?.data?.message)
                errorMsg = error.response.data.message;
            alert(`ERROR: ${errorMsg}`);
            setShowDeleteConfirm(false);
        }
    };

    const moveEvent = async ({ event, start, end }) => {
        try {
            await axios.put(`/api/calendar/events/${event.id}`, {
                start: start.toISOString(),
                end: end.toISOString(),
            });

            const updatedEvents = events.map((e) =>
                e.id === event.id ? { ...e, start, end } : e
            );
            setEvents(updatedEvents);
        } catch (error) {
            console.error("Error moving event:", error);
            alert("Gagal memindahkan event");
            fetchEvents();
        }
    };

    const resetForm = () => {
        setFormData({
            title: "",
            start: "",
            end: "",
            allDay: false,
            description: "",
        });
        setSelectedEvent(null);
    };

    return (
        <>
            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px) scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }
                button:hover {
                    transform: scale(1.05);
                    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
                }
                input:focus, textarea:focus {
                    border-color: #3b82f6;
                    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
                }
                .delete-btn:hover {
                    background: #ef4444 !important;
                    color: white !important;
                }
                .close-btn:hover {
                    background: rgba(255, 255, 255, 0.2);
                }
                .cancel-btn:hover {
                    background: #f1f5f9;
                }
            `}</style>
            
            <div style={styles.container}>
                <div style={{ maxWidth: '1280px', margin: '0 auto' }}>
                    <div style={styles.header}>
                        <div style={styles.headerLeft}>
                            <div style={styles.iconBox}>
                                <CalIcon size={32} color="white" />
                            </div>
                            <div>
                                <h1 style={styles.title}>Kalender CRM</h1>
                                <p style={styles.subtitle}>Kelola jadwal dan event Anda</p>
                            </div>
                        </div>
                        <button
                            onClick={() => {
                                setSelectedEvent(null);
                                setFormData({
                                    title: "",
                                    start: new Date().toISOString().slice(0, 16),
                                    end: new Date(Date.now() + 3600000).toISOString().slice(0, 16),
                                    allDay: false,
                                    description: "",
                                });
                                setShowModal(true);
                            }}
                            style={styles.addButton}
                        >
                            <Plus size={20} />
                            Tambah Event
                        </button>
                    </div>

                    <div style={styles.calendarCard}>
                        <DnDCalendar
                            localizer={localizer}
                            events={events}
                            startAccessor="start"
                            endAccessor="end"
                            style={{ height: 600 }}
                            selectable
                            resizable
                            onSelectSlot={handleSelectSlot}
                            onSelectEvent={handleSelectEvent}
                            onEventDrop={moveEvent}
                            onEventResize={moveEvent}
                            views={["month", "week", "day", "agenda"]}
                        />
                    </div>
                </div>
            </div>

            {showModal && (
                <Modal onClose={() => { setShowModal(false); resetForm(); }} zIndex={10001}>
                    <div style={styles.modalContent}>
                        <div style={styles.modalHeader}>
                            <div style={styles.modalHeaderLeft}>
                                <div style={styles.modalIconBox}>
                                    {selectedEvent ? <CalIcon size={24} color="white" /> : <Plus size={24} color="white" />}
                                </div>
                                <h3 style={styles.modalTitle}>
                                    {selectedEvent ? "Edit Event" : "Buat Event Baru"}
                                </h3>
                            </div>
                            <button
                                onClick={() => { setShowModal(false); resetForm(); }}
                                style={styles.closeButton}
                                className="close-btn"
                            >
                                <X size={24} />
                            </button>
                        </div>

                        <div style={styles.modalBody}>
                            <div style={{ marginBottom: '20px' }}>
                                <label style={styles.label}>
                                    <CalIcon size={16} color="#2563eb" />
                                    Judul Event
                                </label>
                                <input
                                    type="text"
                                    style={styles.input}
                                    placeholder="Masukkan judul event..."
                                    value={formData.title}
                                    onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                    required
                                />
                            </div>

                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px', marginBottom: '20px' }}>
                                <div>
                                    <label style={styles.label}>
                                        <Clock size={16} color="#16a34a" />
                                        Waktu Mulai
                                    </label>
                                    <input
                                        type="datetime-local"
                                        style={styles.input}
                                        value={formData.start}
                                        onChange={(e) => setFormData({ ...formData, end: e.target.value })}
                                        required
                                    />
                                </div>
                                <div>
                                    <label style={styles.label}>
                                        <Clock size={16} color="#ea580c" />
                                        Waktu Selesai
                                    </label>
                                    <input
                                        type="datetime-local"
                                        style={styles.input}
                                        value={formData.end}
                                        onChange={(e) => setFormData({ ...formData, start: e.target.value })}
                                        required
                                    />
                                </div>
                            </div>

                            <div style={{ marginBottom: '20px' }}>
                                <div style={styles.checkboxContainer}>
                                    <label style={styles.checkboxLabel}>
                                        <input
                                            type="checkbox"
                                            style={styles.checkbox}
                                            checked={formData.allDay}
                                            onChange={(e) => setFormData({ ...formData, allDay: e.target.checked })}
                                        />
                                        <span style={{ fontSize: '14px', fontWeight: '500', color: '#334155' }}>
                                            Event seharian penuh
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div style={{ marginBottom: '0' }}>
                                <label style={styles.label}>
                                    <AlignLeft size={16} color="#9333ea" />
                                    Deskripsi <span style={{ color: '#94a3b8', fontWeight: 'normal' }}>(Opsional)</span>
                                </label>
                                <textarea
                                    style={styles.textarea}
                                    rows="4"
                                    placeholder="Tambahkan catatan atau detail event..."
                                    value={formData.description}
                                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                ></textarea>
                            </div>

                            <div style={styles.actions}>
                                <div>
                                    {selectedEvent && (
                                        <button
                                            type="button"
                                            onClick={handleDeleteClick}
                                            style={styles.deleteButton}
                                            className="delete-btn"
                                        >
                                            <Trash2 size={16} />
                                            Hapus Event
                                        </button>
                                    )}
                                </div>
                                <div style={styles.actionsRight}>
                                    <button
                                        type="button"
                                        onClick={() => { setShowModal(false); resetForm(); }}
                                        style={styles.cancelButton}
                                        className="cancel-btn"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        onClick={handleSubmit}
                                        style={styles.saveButton}
                                    >
                                        <Save size={16} />
                                        {selectedEvent ? "Update Event" : "Simpan Event"}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Modal>
            )}

            {showDeleteConfirm && createPortal(
                <div
                    onClick={(e) => {
                        if (e.target === e.currentTarget) setShowDeleteConfirm(false);
                    }}
                    style={{ ...styles.modalOverlay, zIndex: 99999 }}
                >
                    <div onClick={(e) => e.stopPropagation()} style={styles.confirmModal}>
                        <div style={styles.confirmHeader}>
                            <div style={styles.modalIconBox}>
                                <Trash2 size={24} color="white" />
                            </div>
                            <h3 style={{ ...styles.modalTitle, fontSize: '20px' }}>Konfirmasi Hapus</h3>
                        </div>

                        <div style={styles.confirmBody}>
                            <p style={styles.confirmText}>
                                Apakah Anda yakin ingin menghapus event{" "}
                                <strong style={{ color: '#0f172a' }}>"{selectedEvent?.title}"</strong>?
                            </p>
                            <p style={styles.confirmSubtext}>
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>

                        <div style={styles.confirmActions}>
                            <button
                                onClick={() => setShowDeleteConfirm(false)}
                                style={styles.cancelButton}
                                className="cancel-btn"
                            >
                                Batal
                            </button>
                            <button
                                onClick={confirmDelete}
                                style={styles.confirmButton}
                            >
                                <Trash2 size={16} />
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>,
                document.body
            )}
        </>
    );
}