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
   
    function IsInput($submit, $smarty) {
        if (isset($submit) !== true) {
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

    function MainProcess($smarty) {
        // フォームに入力があった場合
        $data_count  = '';
        $screen_name = $_POST['screen_name'];
        
        $result = $this->GetTweets($screen_name);
        $result_data = array();
        //　ここリファクタリング
        if ( $result !== NULL ) {
            // create at : ツイートされた日時のみ取得する
            foreach ( $result as $data) {
                // 鍵付きのアカウントの場合
                if ( is_array( $data ) === false) { break; }
        
                foreach ($data as $key => $val) {
                    // 日付けをキーにして配列に挿入
                    if( $key === 'created_at') {
                        $result_data[] = date("Y/n/j", strtotime($val));
                    } 
                }
            }
        } else {
            $err_msg = 'データが取得できませんでした。';
            $this->ReturnError($smarty, $err_msg);
        }
        
        $date_count = array_count_values($result_data);
        
        // アカウントが存在しない場合・鍵付きである場合
        if ( count( $result_data ) === 0 ) {
            $err_msg = '入力されたアカウントは存在していないか、プライベート設定がされています';
            $this->ReturnError($smarty, $err_msg);
        }

        $err_msg = '';        
        $smarty-> assign('result', $date_count);
        $smarty-> assign('err_msg', $err_msg);
        $smarty-> display('twi_graph.tpl');
    }

}

$twi_graph = new MainGraph();
$smarty    = $twi_graph->Smarty();

if ($_POST) {
    $twi_graph->IsInput($_POST['smt'], $smarty);
    $twi_graph->MainProcess($smarty);
} else {
    $twi_graph->init($smarty);
}
