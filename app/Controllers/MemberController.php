<?php

namespace App\Controllers;

class MemberController extends BaseController
{
    private $session;
    private $memberModel;
    private $memberToRoleModel;

    public function __construct() {
        $this->session = \Config\Services::session();
        $this->memberModel = new \App\Models\MemberModel();
        $this->memberToRoleModel = new \App\Models\MemberToRoleModel();
    }

    // 회원가입
    public function join()
    {
        // 밸리데이션 처리.
        $joinValidation = [
            'username' => 'required|min_length[1]|max_length[20]',
            'password' => 'required|min_length[8]|max_length[20]',
            'nickname' => 'required|min_length[1]|max_length[10]',
        ];
        if (!$this->validate($joinValidation, $this->request->getJSON(true))) {
            return $this->response->setStatusCode(400);
        }

        $username = $this->request->getJSON(true)['username'];
        $password = $this->request->getJSON(true)['password'];
        $nickname = $this->request->getJSON(true)['nickname'];

        // 비밀번호 단방향(해시) 암호화 처리.
        $hashPassword = password_hash($password, PASSWORD_BCRYPT);

        // 기존에 가입된 username이 있는지 조회.
        $member = $this->memberModel->findByUsername($username);
        if ($member) {
            return $this->response->setStatusCode(400);
        }

        // 트랜잭션 시작
        $this->memberModel->db->transStart();

        // 회원정보 INSERT
        $this->memberModel->insert([
            'username' => $username,
            'password' => $hashPassword,
            'nickname' => $nickname
        ]);

        // 역할 맵핑 정보 INSERT
        $this->memberToRoleModel->insert([
            'member_id' => $this->memberModel->getInsertID(),
            'role_id' => 2 // == 'USER'
        ]);

        // 트랜잭션 종료
        $this->memberModel->db->transComplete();

        return $this->response->setStatusCode(201);
    }

    // 로그인
    public function login() {
        $username = $this->request->getJSON(true)['username'];
        $password = $this->request->getJSON(true)['password'];

        $member = $this->memberModel->findByUsername($username);

        if(empty($member)) {
            return $this->response->setStatusCode(400);
        }

        if(!password_verify($password, $member['password'])) {
            return $this->response->setStatusCode(400);
        }

        // 현재 로그인한, 사용자의 역할을 찾아옴.
        $role = $this->memberToRoleModel->where('member_id', $member['member_id'])
            ->join('role', 'role.role_id = member_to_role.role_id')
            ->first();

        // 역할이 없으면, 문제가 있는 부분이므로, 서버에 대한 에러 처리 (DB내용 확인 필요.)
        if(empty($role)) {
            return $this->response->setStatusCode(500);
        }

        // 세션에 데이터 할당하기.
        $this->session->set('member_id', $member['member_id']);
        $this->session->set('nickname', $member['nickname']);
        $this->session->set('role_name', $role['role_name']);

        // 성공적인 응답 처리.
        $this->response->setStatusCode(200);

    }

    // 로그아웃
    public function logout() {
        $this->session->destroy();
        return $this->response->setStatusCode(200);
    }

    // 회원목록 불러오기 (참가자 조회)
    public function getMemberList() {
        $memberList = $this->memberModel
            ->select('member_id, nickname')
            ->findAll();
        return $this->response->setJSON($memberList);
    }


}