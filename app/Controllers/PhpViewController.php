<?php

namespace App\Controllers;

class PhpViewController extends BaseController
{
    private $session;
    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    // 캘린더 메인페이지 리턴
    public function mainCalendarView() {

        // TODO => 공통 처리로 뺼 수 있는지 확인 (middleware로)
        //        if (!$this->session->has('member_id')) {
        //
        //        }

        return view('templates/header')
            .view('pages/calendar')
            .view('templates/footer');
    }

    // 로그인 페이지 리턴
    public function loginView() {

        return view('pages/login');

    }

    // 회원가입 페이지 리턴
    public function joinView() {
        return view('pages/join');
    }



}