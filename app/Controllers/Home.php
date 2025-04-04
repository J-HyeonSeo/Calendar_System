<?php

namespace App\Controllers;

class Home extends BaseController
{

    private $session;

    public function __construct() {
        $this->session = \Config\Services::session();
    }

    public function index(): string
    {
//        if (!$this->session->has('member_id')) {
//
//        }

        return view('templates/header')
            .view('pages/calendar')
            .view('templates/footer');
    }
}
