<?php

namespace App\Controllers;

class ScheduleController extends BaseController
{
    private $session;
    private $scheduleModel;
    private $participantModel;

    public function __construct() {
        $this->session = \Config\Services::session();
        $this->scheduleModel = new \App\Models\ScheduleModel();
        $this->participantModel = new \App\Models\ParticipantModel();
    }

    // 일정 등록
    public function addSchedule() {

        $memberId = $this->session->get('member_id');

        $input = $this->request->getJSON(true);

        $type = $input['type'];
        $title = $input['title'];
        $place = $input['place'];
        $startDt = $input['startDt'];
        $endDt = $input['endDt'];
        $participantList = $input['participantList'];

        $this->scheduleModel->db->transStart();

        $this->scheduleModel->insert([
            'type' => $type,
            'title' => $title,
            'place' => $place,
            'start_dt' => $startDt,
            'end_dt' => $endDt,
            'member_id' => $memberId
        ]);

        foreach ($participantList as $participant) {
            $this->participantModel->insert([
                'member_id' => $participant['memberId'],
                'schedule_id' => $this->scheduleModel->getInsertID()
            ]);
        }

        $this->scheduleModel->db->transComplete();

        return $this->response->setStatusCode(201);
    }

    // 일정 수정
    public function updateSchedule() {

    }

    // 일정 삭제
    public function removeSchedule() {

    }

    // 일정 조회 (사용자 -> 본인 것만, 관리자 -> 전부)
    public function getScheduleList() {

    }

}