<main>
    <div id="calendar"></div>
    <aside id="detail-schedule">

        <div id="close-schedule-btn">X</div>

        <div id="type">회식</div>
        <h4>1차 회식</h4>
        <h6>장소: 닭갈비집</h6>
        <h5>2024.4.1 16:00 ~</h5>
        <h5>2024.4.1 18:30</h5>
        <h4>참가자</h4>
        <div id="participant-view">
            <div class="participant-card">
                Jerry
            </div>
            <div class="participant-card">
                Jason
            </div>
            <div class="participant-card">
                Tom
            </div>
            <div class="participant-card">
                Patrick
            </div>
            <div class="participant-card">
                Chris
            </div>
            <div class="participant-card">
                James
            </div>
            <div class="participant-card">
                Sarah
            </div>
        </div>
        <div id="detail-schedule-btn-wrap">
            <div class="btn" style="background-color: #F18800">수정</div>
            <div class="btn" style="background-color: #B02A2A; margin: 0 10px;">삭제</div>
            <div class="btn" style="background-color: #1F63BA;">복사</div>
        </div>
    </aside>
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
                alert(info.event.extendedProps.place);
            },
            dateClick: function(date) {
                alert(date.dateStr);
            }
        });
        calendar.render();
    });
</script>