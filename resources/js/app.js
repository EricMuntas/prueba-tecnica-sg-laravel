import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import esLocale from '@fullcalendar/core/locales/es';

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    // ── Load user's orders from API ──────────────────────────────────
    fetch('/api/orders', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then(res => {
            if (!res.ok) throw new Error('No autenticado');
            return res.json();
        })
        .then(orders => {
            const events = orders.map(order => ({
                id: order.id,
                title: `Pedido #${order.id} — ${parseFloat(order.cost).toFixed(2)}€`,
                start: order.date + 'T00:00:00',
                allDay: true,
                extendedProps: {
                    cost: order.cost,
                    products: order.products ?? [],
                },
            }));

            // ── Render calendar ──────────────────────────────────────────
            const calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, listPlugin],
                initialView: 'dayGridMonth',
                locale: esLocale,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth',
                },
                events,

                // Dispatch a custom event so any view can react to it
                // without coupling the calendar to Bootstrap modals
                eventClick(info) {
                    const ev = info.event;
                    calendarEl.dispatchEvent(
                        new CustomEvent('order:selected', {
                            bubbles: true,
                            detail: {
                                id: ev.id,
                                date: ev.startStr,
                                cost: ev.extendedProps.cost,
                                products: ev.extendedProps.products,
                            },
                        })
                    );
                },
            });

            calendar.render();
        })
        .catch(() => {
            calendarEl.innerHTML =
                '<p class="text-muted p-3">Inicia sesión para ver tus pedidos en el calendario.</p>';
        });
});