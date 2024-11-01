<?php

class ApiService
{
    private function callUserTop()
    {
        $secret = base64_decode(get_option('wp_monero_miner_secret_key'));

        $url = 'https://api.coinhive.com/user/top?secret=' . $secret;
        $response = json_decode(file_get_contents($url));

        return $response;
    }

    public function getUserTop()
    {
        $data = get_transient('wp_monero_miner_api_user_top');

        if (empty($data)) {
            $response = $this->callUserTop();

            $time = date('d-m-Y H:i');

            $users = array();
            if ($response && $response->success) {
                $users = $response->users;
            }

            $data = array(
                'success' => $response->success,
                'error' => $response->success ? '' : $response->error,
                'time' => $time,
                'users' => $users
            );

            set_transient('wp_monero_miner_api_user_top', $data, 2*60);
        }

        return $data;
    }

    private function callUserList()
    {
        $secret = base64_decode(get_option('wp_monero_miner_secret_key'));

        $url = 'https://api.coinhive.com/user/list?secret=' . $secret;
        $response = json_decode(file_get_contents($url));

        return $response;
    }

    public function getUserList()
    {
        $data = get_transient('wp_monero_miner_api_user_list');

        if (empty($data)) {
            $response = $this->callUserTop();

            $time = date('d-m-Y H:i');

            $users = array();
            if ($response && $response->success) {
                $users = $response->users;
            }

            $data = array(
                'success' => $response->success,
                'error' => $response->success ? '' : $response->error,
                'time' => $time,
                'users' => $users
            );


            set_transient('wp_monero_miner_api_user_list', $data, 2*60);
        }

        return $data;
    }

    private function findUserByName($users, $name) {
        foreach($users as $user) {
            if ($user->name == $name) {
                return $user;
            }
        }
        return null;
    }

    public function getCoinHiveUser($name) {
        $data = $this->getUserList();
        return $this->findUserByName($data['users'], $name);
    }

    public function getWordpressUserFull() {
        $data = $this->getUserList();

        $wpUser = $this->getWordpressUser();

        $wpUser['time'] = $data['time'];
        $wpUser['tokens'] = 0;

        if ($wpUser['loggedIn']) {
            $coinHiveUser = $this->getCoinHiveUser($wpUser['name']);
            if($coinHiveUser != null) {
                $wpUser['tokens'] = $coinHiveUser->total;
            }
        }

        return $wpUser;
    }

    public function getWordpressUser() {
        $userLoggedIn = false;
        $userName = '';

        $currentUser = wp_get_current_user();
        if ($currentUser instanceof WP_User) {
            $userLoggedIn = !(0 == $currentUser->ID);
            $userName = $currentUser->user_login;
        }

        return array(
            'loggedIn' => $userLoggedIn,
            'name' => $userName,
        );
    }
}

?>