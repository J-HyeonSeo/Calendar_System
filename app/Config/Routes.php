<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


/*
    #####################################################################
    #############                                         ###############
    #############          PhpView를 호출하는 Router        ###############
    #############                                         ###############
    #####################################################################
 */

// 메인(캘린더 페이지
$routes->get('/', 'PhpViewController::mainCalendarView');

// 로그인/회원가입 페이지
$routes->get('/login', 'PhpViewController::loginView');
$routes->get('/join', 'PhpViewController::joinView');

// 일정 등록/수정 페이지
$routes->get('/schedule/register-view', 'PhpViewController::scheduleRegisterView');
$routes->get('/schedule/edit-view', 'PhpViewController::scheduleEditView');

// 통계 조회 페이지
$routes->get('/statistics/view', 'PhpViewController::statisticsView');

/*
    #####################################################################
    #############                                         ###############
    #############          Rest Api를 호출하는 Router       ###############
    #############                                         ###############
    #####################################################################
 */
$routes->post('/member/join', 'MemberController::join');
$routes->post('/member/login', 'MemberController::login');
$routes->post('/member', 'MemberController::getMemberList');
