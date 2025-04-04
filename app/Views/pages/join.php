<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>일정관리 시스템 회원가입</title>

    <!-- 빠른 개발을 위한 JQuery 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/join.css">
</head>
<body>

<h1>일정관리 시스템 회원가입</h1>
<form>
    <div id="form-input-wrap">
        <div id="form-label">
            <label for="username">아이디</label>
            <label for="password">비밀번호</label>
            <label for="nickname">닉네임</label>
        </div>
        <div id="form-input">
            <input id="username"/>
            <input id="password" type="password"/>
            <input id="nickname"/>
        </div>
    </div>
    <div class="btn form-btn" onclick="doJoin()">
        회원가입
    </div>
</form>
<div class="btn form-btn" style="margin: 0px auto"
     onclick="window.location.href='/login';">
    로그인
</div>

<script>
    function doJoin() {
        const username = $('#username').val();
        const password = $('#password').val();
        const nickname = $('#nickname').val();

        $.ajax({
            url: '/member/join', // 로그인 API 경로
            type: 'POST', // HTTP 메서드
            data: JSON.stringify({
                username: username,
                password: password,
                nickname: nickname,
            }),
            contentType: 'application/json',
            success: function (response) {
                alert('회원가입에 성공하였습니다.');
                window.location.href = '/login';
            },
            error: function (xhr, status, error) {
                alert('서버에 오류가 발생하였습니다.');
            }
        });
    }
</script>
</body>
</html>