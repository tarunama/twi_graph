<?php
require_once('/Applications/XAMPP/smarty/libs/Smarty.class.php');
require_once('twitteroauth.php');
require_once('conf.php');

Class MainGraph {

    function Smarty() {
        $smarty = new Smarty();
        $smarty-> template_dir = './tpl';
        $smarty-> compile_dir  = './cash';
        return $smarty;
    }

    function init($smarty) {
        $err_msg = '';
        $smarty-> assign('err_msg', $err_msg);
        $smarty-> display('twi_graph.tpl');
    }
   
    function IsInput($screen_name, $smarty) {
        if (empty($screen_name)) {
            $err_msg = 'アカウント名を入力してください'; 
            $this->ReturnError($smarty, $err_msg);
        }
    }

    function ReturnError($smarty, $err_msg) {
        $smarty-> assign('err_msg', $err_msg);
        $smarty-> display('twi_graph.tpl');
        exit();
    }

    function GetTweets($screen_name) {
        // Twitter APIに接続・OAuth認証を行う
        $to = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
        $req = $to->OAuthRequest('https://api.twitter.com/1.1/statuses/user_timeline.json'
            , 'GET', array('screen_name' => $screen_name ) );
        
        return json_decode($req, true);
    }

    function CheckException($result, $smarty) {
        // アカウントが存在しない場合・鍵付きである場合
        if (count($result) === 0) {
            $err_msg = '入力されたアカウントは存在していないか、プライベート設定がされています';
            $this->ReturnError($smarty, $err_msg);
        }
    }

    function IsNull($arg) {
        if ($arg === NULL) {
            $err_msg = 'データが取得できませんでした。';
            $this->ReturnError($smarty, $err_msg);
        }
    }

    function CollectDate($result) {
        if (empty($result)) { return NULL; }
        
        // ツイートされた日時のみ取得する
        $end = count($result);
        $result_data = array();
        
        //　ここなんとかしたい
        for($i = 0; $i < $end; $i++) {
            if (empty($result[$i]['created_at'])) { continue; }
            $date = date("Y/n/j", strtotime($result[$i]['created_at']));
            array_push($result_data, $date);
        }
        return $result_data;
    }

    function MainProcess($smarty, $screen_name) {
        // フォームに入力があった場合
        $data_count = '';
        $err_msg = '';        
        
        $result = $this->GetTweets($screen_name);
        $this->IsNull($result);
        $result_data = $this->CollectDate($result, $smarty);
        $this->CheckException($result_data, $smarty);
        $date_count = array_count_values($result_data);

        $smarty-> assign('result', $date_count);
        $smarty-> assign('err_msg', $err_msg);
        $smarty-> display('twi_graph.tpl');
    }
}

$twi_graph = new MainGraph();
$smarty    = $twi_graph->Smarty();

if ($_POST) {
    $screen_name = htmlspecialchars($_POST['screen_name']);
    $twi_graph->IsInput(trim($screen_name), $smarty);
    $twi_graph->MainProcess($smarty, $screen_name);
} else {
    $twi_graph->init($smarty);
}
