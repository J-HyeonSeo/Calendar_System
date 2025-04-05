<main>
    <div id="calendar"></div>
    <div id="back-drop"></div>
    <aside id="detail-schedule">

        <div id="close-schedule-btn" onclick="closeDetailScheduleModal()">X</div>

        <div id="type">회식</div>
        <h4 id="title">제목</h4>
        <h6 id="place">장소: 닭갈비집</h6>
        <h5 id="startDt">2024.4.1 16:00 ~</h5>
        <h5 id="endDt">2024.4.1 18:30</h5>
        <h4>참가자</h4>
        <div id="participant-view">
            <div class="participant-card">
                Jerry
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

    /*
        FullCalendar 라이브러리 로드.
     */
    var calendar;
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            aspectRatio: 1,
            height: '800px',
            locale: 'ko',
            headerToolbar: {
                left: '',
                center: 'prev,title,next',
                right: 'today'
            },
            buttonText: {
                today: '오늘'
            },
            events: [
            ],
            eventClick: function(info) {
                openDetailScheduleModal(info);
            },
            dateClick: function(date) {
                confirm(`${date.dateStr} 날짜에 일정을 등록하시겠습니까?`);
            },
            datesSet: function(info) {
                settingCalendarEvents();
            }
        });

        calendar.render();
    });

    /*
        #####################################################################
        #############                                         ###############
        #############                일정관리 함수               ###############
        #############                                         ###############
        #####################################################################
     */

    const typeLabelMap = {
        GENERAL: '일반',
        EDUCATION: '교육',
        SEMINAR: '세미나',
        STAFFPARTY: '회식'
        // 필요한 타입들을 계속 추가 가능
    };

    // 상세 일정 모달을 오픈한다.
    function openDetailScheduleModal(info) {
        $('#back-drop').show();
        $('#detail-schedule').show();
        $('#type').text(typeLabelMap[info.event.extendedProps.type]);
        $('#title').text(info.event.title);
        $('#place').text('장소: ' + info.event.extendedProps.place);
        $('#startDt').text(formatEventDate(info.event.start) + ' ~');
        $('#endDt').text(formatEventDate(info.event.end));

        //참가자 채워넣기
        $('#participant-view').empty();

        for (participant of info.event.extendedProps.participantList) {
            const $card = $('<div>').addClass('participant-card').text(participant.nickname);
            $('#participant-view').append($card);
        }
    }

    function closeDetailScheduleModal() {
        $('#back-drop').hide();
        $('#detail-schedule').hide();
    }

    function formatEventDate(date) {
        return `${date.getFullYear()}.${date.getMonth() + 1}.${date.getDate()} ${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')}`;
    }

    function getCalendarYearMonth() {
        const currentDate = calendar.getDate(); // 중심 날짜

        const nowYear = currentDate.getFullYear();
        const nowMonth = currentDate.getMonth(); // 0~11

        const prevDate = new Date(nowYear, nowMonth - 1, 1); // 이전 달
        const nextDate = new Date(nowYear, nowMonth + 1, 1); // 다음 달

        const formatYM = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            return `${y}-${m}`;
        };

        return {
            prevYearMonth: formatYM(prevDate),
            nowYearMonth: formatYM(currentDate),
            nextYearMonth: formatYM(nextDate),
        };
    }

    function settingCalendarEvents() {
        const yearMonthSet = getCalendarYearMonth();

        const startYearMonth = yearMonthSet.prevYearMonth;
        const endYearMonth = yearMonthSet.nextYearMonth;

        calendar.getEvents().forEach(event => event.remove());

        $.ajax({
            url: `/schedule?startYearMonth=${startYearMonth}&endYearMonth=${endYearMonth}`, // 로그인 API 경로
            type: 'GET', // HTTP 메서드
            contentType: 'application/json',
            success: function (response) {
                scheduleEventList = response.scheduleList.map(
                    item => ({
                        ...item,
                        start: item.startDt,
                        end: item.endDt
                    })
                );

                calendar.addEventSource(scheduleEventList);

            },
            error: function () {
                console.error('일정을 조회할 수 없습니다.');
            }
        });

    }


    /*
        #####################################################################
        #############                                         ###############
        #############                이벤트 함수                ###############
        #############                                         ###############
        #####################################################################
     */
    // 백드랍 클릭시 모달 및 백드랍 제거.
    $('#back-drop').click(() => {
        $('#detail-schedule').hide();
        $('#back-drop').hide();
    });


</script>