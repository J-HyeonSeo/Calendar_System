<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>일정관리 시스템 회원가입</title>
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
        alert("TODO 회원가입 연계")
    }
</script>
</body>
</html>