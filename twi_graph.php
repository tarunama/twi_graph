<?php
require_once('/Applications/XAMPP/smarty/libs/Smarty.class.php');
// smartyの設定
$smarty = new Smarty();
$smarty-> template_dir = './tpl';
$smarty-> compile_dir  = './cash';

$err_msg = '';
// フォームに入力がない場合
if (isset($_POST['smt']) !== true) { 
    $smarty-> assign('err_msg', $err_msg);
    $smarty-> display('twi_graph.tpl');
    exit();
}
// フォームに入力があった場合
$data_count  = '';
$screen_name = $_POST['screen_name'];

// 各種キーの読み込み
require_once('twitteroauth.php');
require_once('conf.php');

// Twitter APIに接続・OAuth認証を行う
$to = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$req = $to->OAuthRequest('https://api.twitter.com/1.1/statuses/user_timeline.json'
    , 'GET', array('screen_name' => $screen_name ) );

$result = json_decode($req, true);
$result_data = array();

if ( $result !== NULL ) {
    // create at : ツイートされた日時のみ取得する
    foreach ( $result as $data) {
        // 鍵付きのアカウントの場合
        if ( is_array( $data ) === false) { break; }

        foreach ( $data as $key => $val) {
            // 日付けをキーにして配列に挿入
            if( $key === 'created_at') {
                $result_data[] = date("Y/n/j", strtotime($val));
            } 
        }
    }
} else {
    $err_msg = 'データが取得できませんでした。';
    $smarty-> assign('err_msg', $err_msg);
    $smarty-> display('twi_graph.tpl');
    exit();
}

$date_count = array_count_values($result_data);

// アカウントが存在しない場合・鍵付きである場合
if ( count( $result_data ) === 0 ) {
    $err_msg = '入力されたアカウントは存在していないか、プライベート設定がされています';
}

$smarty-> assign('result', $date_count);
$smarty-> assign('err_msg', $err_msg);
$smarty-> display('twi_graph.tpl');

