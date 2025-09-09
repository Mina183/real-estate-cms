import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        timeZone: 'local',
        events: '/calendar/fetch',
        eventDisplay: 'block',

        // ✅ Add classes based on whether event is past / ongoing
        eventClassNames: function (info) {
        const now   = new Date();
        const start = info.event.start;
        const end   = info.event.end || start;  // if no end, treat as instant event

        if (end < now) return ['is-past'];                // finished
        if (start <= now && end >= now) return ['is-ongoing']; // happening now
        return [];                                        // upcoming
        },

        eventDataTransform: (raw) => {
        // if your API returns scheduled_for instead of start, map it:
        if (raw.scheduled_for && !raw.start) raw.start = raw.scheduled_for;

        // Normalize to ISO if there's a space instead of 'T'
        if (typeof raw.start === 'string' && raw.start.includes(' ') ) {
            raw.start = raw.start.replace(' ', 'T');
        }

        // synthesize an end if missing (default 60 mins or use raw.duration_minutes)
        if (!raw.end && raw.start) {
        const end = new Date(raw.start);
        end.setMinutes(end.getMinutes() + (raw.duration_minutes ?? 60));
        raw.end = end.toISOString();
        }
        return raw;
    },

        // ✅ Redirect on event click (admins only)
        eventClick: function (info) {
            if (window.userRole === 'admin' || window.userRole === 'superadmin') {
                window.location.href = `/admin/meetings/${info.event.id}/edit`;
            } else {
                alert("You don't have permission to edit meetings.");
            }
        },

        // ✅ Custom rendering of events
        eventDidMount: function (info) {
            // Add clock icon to time
            const time = info.el.querySelector('.fc-event-time');
            if (time && !time.textContent.includes('🕒')) {
                time.textContent = `🕒 ${time.textContent}`;
            }

            // Conditionally add status label (partners only)
            const status = info.event.extendedProps.is_accepted;
            const isPartner = !(window.userRole === 'admin' || window.userRole === 'superadmin');

            if (isPartner) {
                const statusLabel = document.createElement('div');
                statusLabel.style.fontSize = '0.75rem';
                statusLabel.style.marginTop = '2px';
                statusLabel.style.opacity = '0.8';

                if (status == 1) {
                    statusLabel.textContent = '✅ Accepted';
                    statusLabel.style.color = 'green';
                } else if (status == 0) {
                    statusLabel.textContent = '❌ Declined';
                    statusLabel.style.color = 'red';
                } else {
                    statusLabel.textContent = '❓ Pending';
                    statusLabel.style.color = 'orange';
                }

                info.el.appendChild(statusLabel);
            }

            // Styling
            info.el.style.lineHeight = '1.2';
            info.el.style.display = 'flex';
            info.el.style.flexDirection = 'column';
            info.el.style.alignItems = 'flex-start';
            info.el.style.backgroundColor = '#fff';
            info.el.style.border = 'none';
            info.el.style.borderRadius = '6px';
            info.el.style.fontWeight = '600';
            info.el.style.fontSize = '0.9rem';
            info.el.style.padding = '4px 6px';
            info.el.title = info.event.extendedProps.description || 'Team meeting';

            // Text styling
            const innerSpans = info.el.querySelectorAll('.fc-event-title, .fc-event-time');
            innerSpans.forEach(span => {
                span.style.color = '#b44c4c';
                span.style.opacity = '1';
            });
        }
    });

    calendar.render();

    // 🔁 Recompute past/ongoing every minute without page reload
    setInterval(() => calendar.rerenderEvents(), 60 * 1000);
});