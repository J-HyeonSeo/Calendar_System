<form id="schedule-form">
    <h4>일정 <?= $mode === 'register' ? '등록' : '수정' ?> 하기</h4>
    <label>종류</label>
    <br>
    <select id="type-input">
        <option value="GENERAL" <?= isset($schedule) ? ($schedule['type'] === 'GENERAL' ? 'selected' : '') : '' ?>>일반</option>
        <option value="EDUCATION" <?= isset($schedule) ? ($schedule['type'] === 'EDUCATION' ? 'selected' : '') : '' ?>>교육</option>
        <option value="SEMINAR" <?= isset($schedule) ? ($schedule['type'] === 'SEMINAR' ? 'selected' : '') : '' ?>>세미나</option>
        <option value="STAFFPARTY" <?=isset($schedule) ? ($schedule['type'] === 'STAFFPARTY' ? 'selected' : '') : '' ?>>회식</option>
    </select>

    <br>

    <label for="title-input">제목</label>
    <br>
    <input id="title-input" value="<?= isset($schedule) ? esc($schedule['title']) : '' ?>" name="title" minlength="1" maxlength="30"/>

    <br>

    <label for="place-input">장소</label>
    <br>
    <input id="place-input" value="<?= isset($schedule) ? esc($schedule['place']) : '' ?>" name="place" minlength="1" maxlength="20"/>

    <br>

    <label for="startDt-input">시작 시간</label>
    <br>
    <input type="datetime-local" value="<?= isset($schedule) ? esc($schedule['startDt']) : (isset($datetime) ? esc($datetime) : '') ?>" id="startDt-input" name="startDt"/>

    <br>

    <label for="endDt-input">종료 시간</label>
    <br>
    <input type="datetime-local" value="<?= isset($schedule) ? esc($schedule['endDt']) : (isset($datetime) ? esc($datetime) : '') ?>" id="endDt-input" name="endDt"/>

    <br>

    <div style="display: flex; align-items: center">
        <label style="margin-right: 10px;">참가자</label>
        <img alt="addButton" onclick="openParticipantModal()" id="add-participant-btn" src="/images/addBtn.png"/>
        <aside id="search-member-modal">
        </aside>
    </div>
    <div id="participant-view" style="background-color: #e1e1e1; margin-top: 8px">
        <?php if (isset($schedule)): ?>
            <?php foreach ($schedule['participantList'] as $participant): ?>
                <div class="participant-card card-custom"
                     data-member-id="<?= esc($participant['memberId']) ?>"
                     onclick="removeParticipantBtn(event)"><?= esc($participant['nickname']) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <br>

    <div class="btn" style="margin: 0 auto;" onclick="addOrUpdateSchedule()"><?= $mode === 'register' ? '등록' : '수정' ?></div>
</form>

<!-- 데이터 처리 스크립트 -->
<script>
    /*
        #####################################################################
        #############                                         ###############
        #############                데이터 관리                ###############
        #############                                         ###############
        #####################################################################
    */
    const selectedMemberList = [
        <?php if (isset($schedule)): ?>
            <?php foreach ($schedule['participantList'] as $participant): ?>
                <?= esc($participant['memberId']) ?>,
            <?php endforeach; ?>
        <?php endif; ?>
    ];

    // 참가자 목록 조회하기.
    $.ajax({
        url: '/member', // 로그인 API 경로
        type: 'GET', // HTTP 메서드
        success: function (response) {
            for (member of response) {
                const $card = $('<div>').addClass('member-content').text(member.nickname)
                    .attr('data-member-id', member.memberId)
                    .on('click', addParticipantBtn);
                $('#search-member-modal').append($card);
            }
        },
        error: function () {
            alert('참가자 정보를 가져오지 못했습니다.');
        }
    });


    // 입력한 데이터가 맞는지 검증하는 함수.
    function validate(requestBody) {

        // 종류 검증
        if (!['GENERAL', 'EDUCATION', 'SEMINAR', 'STAFFPARTY'].includes(requestBody.type)) {
            alert("종류가 올바르지 않습니다.");
            return false;
        }

        // 제목은 1 ~ 30
        if (requestBody.title === undefined ||
            requestBody.title === null ||
            requestBody.title.trim() === '' ||
            requestBody.title.trim().length < 1 ||
            requestBody.title.trim().length > 30
        ) {
            alert("제목은 1 ~ 30 자로 입력해주세요.");
            return false;
        }

        // 장소는 1 ~ 20
        if (requestBody.place === undefined ||
            requestBody.place === null ||
            requestBody.place.trim() === '' ||
            requestBody.place.trim().length < 1 ||
            requestBody.place.trim().length > 20
        ) {
            alert("장소는 1 ~ 20 자로 입력해주세요.");
            return false;
        }

        // 시작 시간은 반드시 입력
        if (requestBody.startDt === undefined ||
            requestBody.startDt === null ||
            requestBody.startDt === ''
        ) {
            alert("시작 시간을 지정해주세요.");
            return false;
        }

        // 종료 시간은 반드시 입력
        if (requestBody.endDt === undefined ||
            requestBody.endDt === null ||
            requestBody.endDt === ''
        ) {
            alert("종료 시간을 지정해주세요.");
            return false;
        }

        // 시작 시간 < 종료 시간
        if (new Date(requestBody.startDt) >= new Date(requestBody.endDt)) {
            alert("시작 시간이 종료 시간와 동일하거나 넘어설 수 없습니다.");
            return false;
        }

        if (requestBody.participantList.length < 1) {
            alert("참가자는 적어도 1명은 존재해야 합니다.");
            return false;
        }

        return true;

    }


    /*
        #####################################################################
        #############                                         ###############
        #############                이벤트 함수                ###############
        #############                                         ###############
        #####################################################################
    */
    function openParticipantModal() {
        const $modal = $('#search-member-modal');

        if ($modal.css('display') === 'none') {
            $modal.show();
        } else {
            $modal.hide();
        }

    }

    function addParticipantBtn(e) {

        // 이벤트로 발생한 데이터와, value를 가져오고, 이를 새롭게 넣어주어야 함.
        // 단 이미 존재하는 데이터는 삽입 금지.

        const memberId = Number(e.target.getAttribute('data-member-id'));
        const nickname = e.target.textContent;

        if (selectedMemberList.includes(memberId)) {
            alert("이미 선택된 참가자 입니다.");
            $('#search-member-modal').hide();
            return;
        }

        const $card = $('<div>').addClass('participant-card').addClass('card-custom').text(nickname)
            .attr('data-member-id', memberId)
            .on('click', removeParticipantBtn);

        $('#participant-view').append($card);
        selectedMemberList.push(memberId);

        $('#search-member-modal').hide();

    }

    function removeParticipantBtn(e) {
        const memberId = Number(e.target.getAttribute('data-member-id'));

        const index = selectedMemberList.indexOf(memberId);
        if (index !== -1) {
            selectedMemberList.splice(index, 1);
        }

        e.target.remove();
    }

    function addOrUpdateSchedule() {

        const type = $('#type-input').val();
        const title = $('#title-input').val();
        const place = $('#place-input').val();
        const startDt = $('#startDt-input').val();
        const endDt = $('#endDt-input').val();
        const participantList = selectedMemberList.map(id => ({ memberId: id }));

        const requestBody = {
            type: type,
            title: title,
            place: place,
            startDt: startDt,
            endDt: endDt,
            participantList
        };

        // 밸리데이션 처리.
        if (!validate(requestBody)) {
            return;
        }

        // 스케줄 등록
        <?php if ($mode === 'register'): ?>
            $.ajax({
                url: '/schedule', // 로그인 API 경로
                type: 'POST', // HTTP 메서드
                headers: {
                    "<?= csrf_header() ?>": "<?= csrf_hash() ?>"
                },
                data: JSON.stringify(requestBody),
                contentType: 'application/json',
                success: function () {
                    alert('스케줄을 등록하였습니다.');
                    window.location.href = '/';
                },
                error: function () {
                    alert('스케줄 등록에 실패하였습니다.');
                }
            });
        <?php else: ?>

            // 스케줄 수정
            $.ajax({
                url: `/schedule/<?= esc($schedule['scheduleId']) ?>`,
                type: 'PUT', // HTTP 메서드
                headers: {
                    "<?= csrf_header() ?>": "<?= csrf_hash() ?>"
                },
                data: JSON.stringify(requestBody),
                contentType: 'application/json',
                success: function () {
                    alert('스케줄을 수정하였습니다.');
                    window.location.href = '/';
                },
                error: function () {
                    alert('스케줄 수정에 실패하였습니다.');
                }
            });
        <?php endif; ?>

    }

</script>