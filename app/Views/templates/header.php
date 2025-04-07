<?php $session = session(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- 스타일 지정 -->
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/styles.css">

    <!-- 캘린더 라이브러리 설정 -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

    <!-- 차트 라이브러리 설정 -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>

    <!-- 빠른 개발을 위한 JQuery 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <title>일정관리 시스템</title>
</head>
<body>
    <header>
        <a href="/">일정관리 시스템</a>
        <nav>
            <ol>
                <li><a href="/">메인 페이지</a></li>
                <li><a href="/schedule/register-view">일정 등록 하기</a></li>
                <?php if ($session->get('role_name') === 'ADMIN'): ?>
                    <li><a href="/statistics/view">일정 통계 페이지</a></li>
                <?php endif; ?>
            </ol>
        </nav>

        <div id="profile">
            <h3><?= esc($session->get('nickname'))?>님</h3>
            <div class="btn" style="background-color: #F18800;" onclick="doLogout()">로그아웃</div>
        </div>
    </header>

    <script>
        // 로그아웃을 위한 스크립트.
        function doLogout() {
            $.ajax({
                url: '/member/logout', // 로그인 API 경로
                type: 'POST', // HTTP 메서드
                headers: {
                    "<?= csrf_header() ?>": "<?= csrf_hash() ?>"
                },
                success: function (response) {
                    alert('로그아웃 하였습니다.');
                    window.location.href = '/login';
                },
                error: function (xhr, status, error) {
                    alert('서버에 문제가 발생하여, 로그아웃에 실패하였습니다.');
                }
            });
        }

    </script>