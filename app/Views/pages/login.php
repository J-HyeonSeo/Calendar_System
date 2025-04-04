<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>일정관리 시스템 로그인</title>

    <!-- 빠른 개발을 위한 JQuery 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="/public/css/common.css">
    <link rel="stylesheet" href="/public/css/login.css">
</head>
<body>

    <h1>일정관리 시스템 로그인</h1>
    <form>
        <div id="form-input-wrap">
            <div id="form-label">
                <label for="username">아이디</label>
                <label for="password">비밀번호</label>
            </div>
            <div id="form-input">
                <input id="username"/>
                <input id="password" type="password"/>
            </div>
        </div>
        <div class="btn form-btn" onclick="doLogin()">
            로그인
        </div>
    </form>
    <div class="btn form-btn" style="margin: 0px auto"
         onclick="window.location.href='/join';">
        회원가입
    </div>

    <script>
        function doLogin() {
            const username = $('#username').val();
            const password = $('#password').val();

            $.ajax({
                url: '/member/login', // 로그인 API 경로
                type: 'POST', // HTTP 메서드
                data: JSON.stringify({
                    username: username,
                    password: password
                }),
                contentType: 'application/json',
                success: function (response) {
                    alert('로그인에 성공하였습니다.');
                    window.location.href = '/';
                },
                error: function (xhr, status, error) {
                    alert('잘못된 아이디 또는 비밀번호 입니다.');
                }
            });
        }
    </script>
</body>
</html>