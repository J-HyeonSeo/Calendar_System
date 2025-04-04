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
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
                {
                    type: '회식',
                    title: '1차 회식',
                    start: '2025-04-04T12:00:00',
                    place: '닭갈비집',
                    end: '2025-04-05T12:00:00',
                    participantList: [
                        {
                            memberId: 1,
                            nickname: 'Jerry'
                        },
                        {
                            memberId: 2,
                            nickname: 'Tom'
                        },
                    ]
                },
                {
                    type: '교육',
                    title: '2차 회식',
                    place: '닭갈비집2',
                    start: '2025-04-04T12:00:00',
                    end: '2025-04-04T13:00:00',
                    participantList: [
                        {
                            memberId: 1,
                            nickname: 'Sarah'
                        },
                        {
                            memberId: 2,
                            nickname: 'Chris'
                        },
                    ]
                },
                {
                    type: '일반',
                    title: '3차 회식',
                    place: '닭갈비집3',
                    start: '2025-04-04T12:00:00',
                    end: '2025-04-04T13:00:00',
                    participantList: [
                        {
                            memberId: 1,
                            nickname: 'Jerry'
                        },
                        {
                            memberId: 2,
                            nickname: 'Jason'
                        },
                    ]
                },
                {
                    type: '세미나',
                    title: '3차 회식',
                    place: '닭갈비집3',
                    start: '2025-04-04T12:00:00',
                    end: '2025-04-04T13:00:00',
                    participantList: [
                        {
                            memberId: 1,
                            nickname: 'Jerry'
                        },
                        {
                            memberId: 2,
                            nickname: 'Patrick'
                        },
                    ]
                },
            ],
            eventClick: function(info) {
                openDetailScheduleModal(info);
            },
            dateClick: function(date) {
                confirm(`${date.dateStr} 날짜에 일정을 등록하시겠습니까?`);
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

    // 상세 일정 모달을 오픈한다.
    function openDetailScheduleModal(info) {
        $('#back-drop').show();
        $('#detail-schedule').show();
        $('#type').text(info.event.extendedProps.type);
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