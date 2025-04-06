<?php

namespace App\Controllers;

use CodeIgniter\Database\Query;

class StatisticsController extends BaseController
{
    private $scheduleModel;

    public function __construct() {
        $this->scheduleModel = new \App\Models\ScheduleModel();
    }

    // 일별 통계
    public function getStatisticsDay() {

        if (!$this->dataValidate()) return $this->response->setStatusCode(400);

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $pQuery = $this->scheduleModel->db->prepare(static function ($db) {

            $sql = "
            -- 일별 조회 쿼리
            WITH RECURSIVE DATE_RANGE AS (
                SELECT DATE(:startDate:) AS dt
                UNION ALL
                SELECT DATE_ADD(dt, INTERVAL 1 DAY)
                FROM DATE_RANGE
                WHERE dt < :endDate:
            ),
            SCHEDULE_DURATION AS (
                SELECT
                    D.dt AS schedule_date,
                    CASE
                        -- 데이터가 없으면, 0분
                        WHEN S.schedule_id IS NULL THEN 0
                        -- 둘 다 같은 날이면, end_dt - start_dt 값으로 계산
                        WHEN DATE(S.start_dt) = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, S.end_dt)
                        -- 시작 날짜가 오늘이면, 다음날 자정에서 - start_dt를 계산하여, start_dt부터 다음날 자정까지의 시간을 계산.
                        WHEN D.dt = DATE(S.start_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, DATE_ADD(DATE(S.start_dt), INTERVAL 1 DAY))
                        -- 종료 날짜가 오늘이면, end_dt에서 오늘 자정의 차이를 계산하여, 오늘 자정 ~ 마지막 시간 까지의 차이를 계산함.
                        WHEN D.dt = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, DATE(S.end_dt), S.end_dt)
                        ELSE 1440
                    END AS minutes
                FROM DATE_RANGE D
                LEFT JOIN schedule S
                    ON D.dt BETWEEN DATE(S.start_dt) AND DATE(S.end_dt)
            )
            SELECT
                schedule_date AS xData,
                ROUND(SUM(minutes) / 60, 1) AS yData
            FROM SCHEDULE_DURATION
            GROUP BY schedule_date
            ORDER BY schedule_date;
            ";

            return (new Query($db))->setQuery($sql);
        });

        $data = $pQuery->execute($startDate, $endDate)->getResult();

        return $this->response->setJSON([
            'chartList' => $data
        ]);
    }

    // 월별 통계
    public function getStatisticsMonth() {

        if (!$this->dataValidate()) return $this->response->setStatusCode(400);

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $pQuery = $this->scheduleModel->db->prepare(static function ($db) {

            $sql = "
            -- 월별 조회 쿼리
            WITH RECURSIVE DATE_RANGE AS (
                SELECT DATE(:startDate:) AS dt
                UNION ALL
                SELECT DATE_ADD(dt, INTERVAL 1 DAY)
                FROM DATE_RANGE
                WHERE dt < :endDate:
            ),
            SCHEDULE_DURATION AS (
                SELECT
                    DATE_FORMAT(D.dt, '%Y-%m') AS schedule_month,
                    CASE
                        -- 데이터가 없으면, 0분
                        WHEN S.schedule_id IS NULL THEN 0
                        -- 둘 다 같은 날이면, end_dt - start_dt 값으로 계산
                        WHEN DATE(S.start_dt) = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, S.end_dt)
                        -- 시작 날짜가 오늘이면, 다음날 자정에서 - start_dt를 계산하여, start_dt부터 다음날 자정까지의 시간을 계산.
                        WHEN D.dt = DATE(S.start_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, DATE_ADD(DATE(S.start_dt), INTERVAL 1 DAY))
                        -- 종료 날짜가 오늘이면, end_dt에서 오늘 자정의 차이를 계산하여, 오늘 자정 ~ 마지막 시간 까지의 차이를 계산함.
                        WHEN D.dt = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, DATE(S.end_dt), S.end_dt)
                        ELSE 1440
                    END AS minutes
                FROM DATE_RANGE D
                LEFT JOIN schedule S
                    ON D.dt BETWEEN DATE(S.start_dt) AND DATE(S.end_dt)
            )
            SELECT
                schedule_month AS xData,
                ROUND(SUM(minutes) / 60, 1) AS yData
            FROM SCHEDULE_DURATION
            GROUP BY schedule_month
            ORDER BY schedule_month;
            ";

            return (new Query($db))->setQuery($sql);
        });

        $data = $pQuery->execute($startDate, $endDate)->getResult();

        return $this->response->setJSON([
            'chartList' => $data
        ]);
    }

    //
    public function getStatisticsParticipant() {

        if (!$this->dataValidate()) return $this->response->setStatusCode(400);

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $pQuery = $this->scheduleModel->db->prepare(static function ($db) {

            $sql = "
            -- 참가자별 조회 쿼리
            WITH RECURSIVE DATE_RANGE AS (
                SELECT DATE(:startDate:) AS dt
                UNION ALL
                SELECT DATE_ADD(dt, INTERVAL 1 DAY)
                FROM DATE_RANGE
                WHERE dt < :endDate:
            ),
            SCHEDULE_DURATION AS (
                SELECT
                    DATE_FORMAT(D.dt, '%Y-%m') AS schedule_month,
                    P.member_id AS member_id,
                    CASE
                        -- 데이터가 없으면, 0분
                        WHEN S.schedule_id IS NULL THEN 0
                        -- 둘 다 같은 날이면, end_dt - start_dt 값으로 계산
                        WHEN DATE(S.start_dt) = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, S.end_dt)
                        -- 시작 날짜가 오늘이면, 다음날 자정에서 - start_dt를 계산하여, start_dt부터 다음날 자정까지의 시간을 계산.
                        WHEN D.dt = DATE(S.start_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, DATE_ADD(DATE(S.start_dt), INTERVAL 1 DAY))
                        -- 종료 날짜가 오늘이면, end_dt에서 오늘 자정의 차이를 계산하여, 오늘 자정 ~ 마지막 시간 까지의 차이를 계산함.
                        WHEN D.dt = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, DATE(S.end_dt), S.end_dt)
                        ELSE 1440
                    END AS minutes
                FROM DATE_RANGE D
                JOIN schedule S
                    ON D.dt BETWEEN DATE(S.start_dt) AND DATE(S.end_dt)
                LEFT JOIN participant P
                    ON P.schedule_id = S.schedule_id
            )
            SELECT
                M.nickname AS xData,
                ROUND(SUM(SD.minutes) / 60, 1) AS yData
            FROM SCHEDULE_DURATION SD
            JOIN member M ON M.member_id = SD.member_id
            GROUP BY SD.member_id
            ORDER BY yData DESC;
            ";

            return (new Query($db))->setQuery($sql);
        });

        $data = $pQuery->execute($startDate, $endDate)->getResult();

        return $this->response->setJSON([
            'chartList' => $data
        ]);
    }

    public function getStatisticsPlace() {

        if (!$this->dataValidate()) return $this->response->setStatusCode(400);

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $pQuery = $this->scheduleModel->db->prepare(static function ($db) {

            $sql = "
            -- 장소별 통계 조회
            WITH RECURSIVE DATE_RANGE AS (
                SELECT DATE(:startDate:) AS dt
                UNION ALL
                SELECT DATE_ADD(dt, INTERVAL 1 DAY)
                FROM DATE_RANGE
                WHERE dt < :endDate:
            ),
            SCHEDULE_DURATION AS (
                SELECT
                    DATE_FORMAT(D.dt, '%Y-%m') AS schedule_month,
                    place,
                    CASE
                        -- 데이터가 없으면, 0분
                        WHEN S.schedule_id IS NULL THEN 0
                        -- 둘 다 같은 날이면, end_dt - start_dt 값으로 계산
                        WHEN DATE(S.start_dt) = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, S.end_dt)
                        -- 시작 날짜가 오늘이면, 다음날 자정에서 - start_dt를 계산하여, start_dt부터 다음날 자정까지의 시간을 계산.
                        WHEN D.dt = DATE(S.start_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, DATE_ADD(DATE(S.start_dt), INTERVAL 1 DAY))
                        -- 종료 날짜가 오늘이면, end_dt에서 오늘 자정의 차이를 계산하여, 오늘 자정 ~ 마지막 시간 까지의 차이를 계산함.
                        WHEN D.dt = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, DATE(S.end_dt), S.end_dt)
                        ELSE 1440
                    END AS minutes
                FROM DATE_RANGE D
                JOIN schedule S
                    ON D.dt BETWEEN DATE(S.start_dt) AND DATE(S.end_dt)
            )
            SELECT
                place AS xData,
                ROUND(SUM(minutes) / 60, 1) AS yData
            FROM SCHEDULE_DURATION
            GROUP BY place
            ORDER BY yData DESC;
            ";

            return (new Query($db))->setQuery($sql);
        });

        $data = $pQuery->execute($startDate, $endDate)->getResult();

        return $this->response->setJSON([
            'chartList' => $data
        ]);
    }

    public function getStatisticsType() {

        if (!$this->dataValidate()) return $this->response->setStatusCode(400);

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $pQuery = $this->scheduleModel->db->prepare(static function ($db) {

            $sql = "
            -- 종류별 통계 조회
            WITH RECURSIVE DATE_RANGE AS (
                SELECT DATE(:startDate:) AS dt
                UNION ALL
                SELECT DATE_ADD(dt, INTERVAL 1 DAY)
                FROM DATE_RANGE
                WHERE dt < :endDate:
            ),
            SCHEDULE_DURATION AS (
                SELECT
                    DATE_FORMAT(D.dt, '%Y-%m') AS schedule_month,
                    type,
                    CASE
                        -- 데이터가 없으면, 0분
                        WHEN S.schedule_id IS NULL THEN 0
                        -- 둘 다 같은 날이면, end_dt - start_dt 값으로 계산
                        WHEN DATE(S.start_dt) = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, S.end_dt)
                        -- 시작 날짜가 오늘이면, 다음날 자정에서 - start_dt를 계산하여, start_dt부터 다음날 자정까지의 시간을 계산.
                        WHEN D.dt = DATE(S.start_dt) THEN TIMESTAMPDIFF(MINUTE, S.start_dt, DATE_ADD(DATE(S.start_dt), INTERVAL 1 DAY))
                        -- 종료 날짜가 오늘이면, end_dt에서 오늘 자정의 차이를 계산하여, 오늘 자정 ~ 마지막 시간 까지의 차이를 계산함.
                        WHEN D.dt = DATE(S.end_dt) THEN TIMESTAMPDIFF(MINUTE, DATE(S.end_dt), S.end_dt)
                        ELSE 1440
                    END AS minutes
                FROM DATE_RANGE D
                JOIN schedule S
                    ON D.dt BETWEEN DATE(S.start_dt) AND DATE(S.end_dt)
            )
            SELECT
                REPLACE(REPLACE(REPLACE(REPLACE(type, 'GENERAL', '일반'), 'EDUCATION', '교육'), 'SEMINAR', '세미나'), 'STAFFPARTY', '회식') AS xData,
                ROUND(SUM(minutes) / 60, 1) AS yData
            FROM SCHEDULE_DURATION
            GROUP BY type
            ORDER BY yData DESC;
            ";

            return (new Query($db))->setQuery($sql);
        });

        $data = $pQuery->execute($startDate, $endDate)->getResult();

        return $this->response->setJSON([
            'chartList' => $data
        ]);
    }



    /*
    #####################################################################
    #############                                         ###############
    #############            데이터를 가져오는 부분           ###############
    #############                                         ###############
    #####################################################################
    */
    private function dataValidate() {

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $dateValidation = [
            'startDate' => 'required|date',
            'endDate' => 'required|date'
        ];

        // 날짜가 아닌 경우, 400오류
        if (!$this->validate($dateValidation, $data)) {
            return false;
        }

        // 시작 범위 <= 종료 범위에 해당하는 날짜여야 함. 아니면, 400에러.
        if (strtotime($startDate) > strtotime($endDate)) {
            return false;
        }

        return true;
    }

}