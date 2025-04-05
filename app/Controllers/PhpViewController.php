<?php

namespace App\Controllers;

class PhpViewController extends BaseController
{
    private $session;
    private $scheduleModel;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->scheduleModel = new \App\Models\ScheduleModel();
    }

    // 캘린더 메인페이지 리턴
    public function mainCalendarView() {
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

    // 일정 등록 페이지 리턴
    public function scheduleRegisterView() {

        $data = [
            'datetime' => $this->request->getGet('datetime')
        ];

        return view('templates/header', $data).
            view('pages/schedule-reg-form')
            .view('templates/footer');
    }

    // 일정 등록 페이지 리턴 (복사 데이터 적용)
    public function scheduleCopyView($scheduleId) {

        $schedule = $this->getSchedule($scheduleId);

        if (!$schedule) {
            return $this->response->setStatusCode(400);
        }

        $data = [
            'schedule' => $this->getSchedule($scheduleId)[0]
        ];

        return view('templates/header', $data).
            view('pages/schedule-copy-form')
            .view('templates/footer');
    }

    // 일정 수정 페이지 리턴
    public function scheduleEditView($scheduleId) {

        $schedule = $this->getSchedule($scheduleId);

        if (!$schedule) {
            return $this->response->setStatusCode(400);
        }

        $data = [
            'schedule' => $this->getSchedule($scheduleId)[0]
        ];

        return view('templates/header', $data).
            view('pages/schedule-edit-form')
            .view('templates/footer');
    }

    // 통계 조회 페이지 리턴
    public function statisticsView() {

        // TODO => 관리자가 아닌 경우, 리다이렉션

        return view('templates/header').
            view('pages/statistics')
            .view('templates/footer');
    }



    /*
    #####################################################################
    #############                                         ###############
    #############            데이터를 가져오는 부분           ###############
    #############                                         ###############
    #####################################################################
 */

    // 일정 데이터 단건 조회.
    private function getSchedule($scheduleId) {

        $memberId = $this->session->get('memberId');
        $role = $this->session->get('role_name');

        if ($role == 'ADMIN') {
            $data = $this->scheduleModel
                ->select('
                    schedule.schedule_id,
                    schedule.title,
                    schedule.start_dt,
                    schedule.end_dt,
                    schedule.place,
                    schedule.type,
                    participant.member_id AS participant_member_id,
                    member.nickname
                ')
                ->where('schedule.schedule_id', $scheduleId)
                ->join('participant', 'participant.schedule_id = schedule.schedule_id')
                ->join('member', 'member.member_id = participant.member_id')
                ->findAll();
        } else {
            $data = $this->scheduleModel
                ->select('
                    schedule.schedule_id,
                    schedule.title,
                    schedule.start_dt,
                    schedule.end_dt,
                    schedule.place,
                    schedule.type,
                    participant.member_id AS participant_member_id,
                    member.nickname
                ')
                ->where('schedule.member_id', $memberId)
                ->where('schedule.schedule_id', $scheduleId)
                ->join('participant', 'participant.schedule_id = schedule.schedule_id')
                ->join('member', 'member.member_id = participant.member_id')
                ->findAll();
        }

        // 가공된 결과 저장할 배열
        $grouped = [];

        foreach ($data as $row) {
            $scheduleId = $row['schedule_id'];

            // 스케줄이 처음 등장하면 초기화
            if (!isset($grouped[$scheduleId])) {
                $grouped[$scheduleId] = [
                    'scheduleId' => $scheduleId,
                    'title'      => $row['title'],
                    'startDt'    => $row['start_dt'],
                    'endDt'      => $row['end_dt'],
                    'place'      => $row['place'],
                    'type'       => $row['type'],
                    'participantList' => []
                ];
            }

            // 자식 데이터 추가
            $grouped[$scheduleId]['participantList'][] = [
                'memberId' => $row['participant_member_id'],
                'nickname'      => $row['nickname'],
            ];
        }

        // 인덱스가 scheduleId인 상태니까 array_values로 인덱스 리셋
        return array_values($grouped);

    }


}