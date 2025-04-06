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

        // 밸리데이션
        $scheduleValidation = [
            'type' => 'required|in_list[GENERAL,EDUCATION,SEMINAR,STAFFPARTY]',
            'title' => 'required|min_length[1]|max_length[30]',
            'place' => 'required|min_length[1]|max_length[20]',
            'startDt' => 'required|date',
            'endDt' => 'required|date',
            'participantList' => 'required',
        ];
        if (!$this->validate($scheduleValidation, $this->request->getJSON(true))) {
            return $this->response->setStatusCode(400);
        }

        $memberId = $this->session->get('member_id');

        $input = $this->request->getJSON(true);

        $type = $input['type'];
        $title = $input['title'];
        $place = $input['place'];
        $startDt = $input['startDt'];
        $endDt = $input['endDt'];
        $participantList = $input['participantList'];

        // 논리적인 밸리데이션
        if (count($participantList) < 1) {
            return $this->response->setStatusCode(400);
        }
        if (strtotime($startDt) >= strtotime($endDt)) {
            return $this->response->setStatusCode(400);
        }

        // 하단 부터 등록 로직 수행.

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
    public function updateSchedule($scheduleId) {

        // 밸리데이션
        $scheduleValidation = [
            'type' => 'required|in_list[GENERAL,EDUCATION,SEMINAR,STAFFPARTY]',
            'title' => 'required|min_length[1]|max_length[30]',
            'place' => 'required|min_length[1]|max_length[20]',
            'startDt' => 'required|date',
            'endDt' => 'required|date',
            'participantList' => 'required',
        ];
        if (!$this->validate($scheduleValidation, $this->request->getJSON(true))) {
            return $this->response->setStatusCode(400);
        }

        $role = $this->session->get('role_name');
        $memberId = $this->session->get('member_id');

        $input = $this->request->getJSON(true);

        $type = $input['type'];
        $title = $input['title'];
        $place = $input['place'];
        $startDt = $input['startDt'];
        $endDt = $input['endDt'];
        $participantList = $input['participantList'];

        // 논리적인 밸리데이션
        if (count($participantList) < 1) {
            return $this->response->setStatusCode(400);
        }
        if (strtotime($startDt) >= strtotime($endDt)) {
            return $this->response->setStatusCode(400);
        }

        $schedule = $this->scheduleModel->find($scheduleId);

        // 없으면, 400에러.
        if (empty($schedule)) {
            return $this->response->setStatusCode(400);
        }

        // 일반 사용자인 경우에는, 권한 체크.
        if ($role == 'USER' && $schedule['member_id'] != $memberId) {
            return $this->response->setStatusCode(400);
        }

        // 스케줄 내용 수정. ========================================================
        $this->scheduleModel->db->transStart();
        $this->scheduleModel->update($scheduleId, [
            'type' => $type,
            'title' => $title,
            'place' => $place,
            'start_dt' => $startDt,
            'end_dt' => $endDt,
        ]);

        // 참가자 조회 하기.
        $prevParticipantList = $this->participantModel->where('schedule_id', $scheduleId)->findAll();

        // 참가자 제거 하기.
        foreach ($prevParticipantList as $prevParticipant) {
            $this->participantModel->delete($prevParticipant['participant_id']);
        }

        // 참가자 다시 추가.
        foreach ($participantList as $participant) {
            $this->participantModel->insert([
                'member_id' => $participant['memberId'],
                'schedule_id' => $scheduleId
            ]);
        }
        $this->scheduleModel->db->transComplete();
        // 끝 ======================================================================

        return $this->response->setStatusCode(200);
    }

    // 일정 삭제
    public function removeSchedule($scheduleId) {

        $memberId = $this->session->get('member_id');
        $roleName = $this->session->get('role_name');

        $schedule = $this->scheduleModel->find($scheduleId);

        if (empty($schedule)) {
            return $this->response->setStatusCode(400);
        }

        if ($roleName == 'USER' && $schedule['member_id'] != $memberId) {
            return $this->response->setStatusCode(400);
        }

        // 참가자는 DB CASCADE로 처리함.
        $this->scheduleModel->delete($scheduleId);

        return $this->response->setStatusCode(200);
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