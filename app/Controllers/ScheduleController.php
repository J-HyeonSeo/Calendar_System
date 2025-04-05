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

        // 시작 연월 가져오기
        $startYearMonth = $this->request->getGet('startYearMonth');

        // 종료 연월 가져오기
        $endYearMonth = $this->request->getGet('endYearMonth');

        $memberId = $this->session->get('member_id');
        $role = $this->session->get('role_name');

        // 범위 조회를 위한 날짜 생성
        $startDate = date('Y-m-01 00:00:00', strtotime($startYearMonth));
        $endDate = date('Y-m-t 23:59:59', strtotime($endYearMonth));

        if ($role === 'ADMIN') { // 관리자 일정 조회
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
                ->where('start_dt >=', $startDate)
                ->where('end_dt <=', $endDate)
                ->join('participant', 'participant.schedule_id = schedule.schedule_id')
                ->join('member', 'member.member_id = participant.member_id')
                ->findAll();

        } else { // 사용자 일정 조회
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
                ->where('start_dt >=', $startDate)
                ->where('end_dt <=', $endDate)
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
        $finalData = array_values($grouped);

        return $this->response->setJSON([
            'scheduleList' => $finalData
        ]);
    }

}