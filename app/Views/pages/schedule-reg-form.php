<form id="schedule-form">
    <h4>일정 등록 하기</h4>
    <label>종류</label>
    <br>
    <select>
        <option value="GENERAL">일반</option>
        <option value="EDUCATION">교육</option>
        <option value="SEMINAR">세미나</option>
        <option value="STAFFPARTY">회식</option>
    </select>

    <br>

    <label for="title-input">제목</label>
    <br>
    <input id="title-input" name="title"/>

    <br>

    <label for="place-input">장소</label>
    <br>
    <input id="place-input" name="place"/>

    <br>

    <label for="startDt-input">시작 시간</label>
    <br>
    <input type="datetime-local" id="startDt-input" name="startDt"/>

    <br>

    <label for="endDt-input">종료 시간</label>
    <br>
    <input type="datetime-local" id="endDt-input" name="endDt"/>

    <br>

    <div style="display: flex; align-items: center">
        <label style="margin-right: 10px;">참가자</label>
        <img alt="addButton" onclick="addParticipantBtn()" id="add-participant-btn" src="/public/images/addBtn.png"/>
        <aside id="search-member-modal">
            <div class="member-content">Jerry</div>
            <div class="member-content">Patrick</div>
            <div class="member-content">Sarah</div>
            <div class="member-content">Jason</div>
            <div class="member-content">Jason</div>
            <div class="member-content">Jason</div>
        </aside>
    </div>
    <div id="participant-view" style="background-color: #e1e1e1; margin-top: 8px">
        <div class="participant-card card-custom" onclick="removeParticipantBtn(event)">Jerry</div>

    </div>
    <br>

    <div class="btn" style="margin: 0 auto;">등록</div>
</form>

<!-- 데이터 처리 스크립트 -->
<script>
    /*
        #####################################################################
        #############                                         ###############
        #############                데이터 구성                ###############
        #############                                         ###############
        #####################################################################
    */


    /*
        #####################################################################
        #############                                         ###############
        #############                이벤트 함수                ###############
        #############                                         ###############
        #####################################################################
    */
    function addParticipantBtn() {
        if ($('#search-member-modal').css('display') === 'none') {
            $('#search-member-modal').show();
        } else {
            $('#search-member-modal').hide();
        }

        // TODO member 조회 API 연계
    }

    function removeParticipantBtn(e) {
        e.target.remove();
    }

</script>