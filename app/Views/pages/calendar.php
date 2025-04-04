<main>
    <div id="calendar"></div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            aspectRatio: 1,
            height: '800px',
            locale: 'ko',
            timeZone: 'UTC',
            headerToolbar: {
                left: '',
                center: 'prev,title,next',
                right: 'today'
            },
            buttonText: {
                today: '오늘'
            },
            events: [
                {
                    title: '1차 회식',
                    start: '2025-04-04T12:00:00',
                    place: '닭갈비집',
                    end: '2025-04-05T12:00:00'
                },
                {
                    title: '1차 회식',
                    place: '닭갈비집',
                    start: '2025-04-04T12:00:00'
                },
                {
                    title: '1차 회식',
                    place: '닭갈비집',
                    start: '2025-04-04T12:00:00'
                },
            ],
            eventClick: function(info) {
                alert(info.event.extendedProps.place)
            }
        });
        calendar.render();
    });
</script>