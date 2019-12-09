<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php_error.log');

$debug_flg = true;
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

// エラーメッセージ
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（確認用）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('MSG18', '選択してください');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', '購入しました！相手と連絡を取りましょう！');


session_save_path("/var/tmp");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();



function debugLogStart(){
    debug('----------------------画面表示開始');
    debug('セッションID：'.session_id());
    debug('現在日時タイムスタンプ'.time());
    if(!empty($_SESSION['login_date']) && (!empty($_SESSION['login_limit']))){
        debug('ログイン期限日時タイムスタンプ'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}


// バリデーション
$err_msg = array();

// 入力必須チェック
function validRequired($str,$key){
    global $err_msg;
    if(empty($str)){
        $err_msg[$key] = MSG01;
    }
}

    // メールの形式チェック
function validEmail($str, $key){
    global $err_msg;
    if(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",$str)){
        $err_msg[$key] = MSG02;
    }
}

// 最小文字数チェック
function validMinLen($str, $key, $min = 6){
    global $err_msg;
    if(mb_strlen($str) < $min){
        $err_msg[$key] = MSG05;
    }
}

// 最大文字数チェック
function validMaxLen($str, $key, $max = 256){
    global $err_msg;
    if(mb_strlen($str) > $max){
        $err_msg[$key] = MSG06;
    }
}

// 半角英数字チェック
function validHalf($str, $key){
    global $err_msg;
    if(!preg_match("/^[a-zA-Z0-9@]+$/",$str)){
        $err_msg[$key] = MSG04;
    }
}

//半角数字チェック（数字のみ）
function validNumber($str, $key){
    if(!preg_match("/[0-9０-９,]+/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG17;
    }
}

// 同値チェック
function validMatch($str1, $str2, $key){
    global $err_msg;
    if($str1 !== $str2){
        $err_msg[$key] = MSG03;
    }
}

// 重複チェック
function validEmailDup($email){
    global $err_msg;
        try {

            $dbh = dbConnect();

            $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);

            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty(array_shift($result))){
                debug('このEmailはすでに登録されています');
                $err_msg['email'] = MSG08;
            }

        } catch (Exception $e){
            error_log('エラー発生：' .$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

// 電話番号形式チェック
function validTel($str, $key){
    if(!preg_match("/^(([0-9]{3}-[0-9]{4})|([0-9]{3}-[0-9]{4}-[0-9]{4})|([0-9]{7})|([0-9]{11}))$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }
}

// 郵便番号形式チェック
function validZip($str, $key){
    if(!preg_match("/^([0-9]{7})?$/i",$str)){
        global $err_msg;
        $err_msg[$key] = MSG11;
    }
}

//固定長チェック
function validLength($str, $key, $length = 8){
    if(mb_strlen($str) !== $length){
        global $err_msg;
        $err_msg[$key] = $len.MSG14;
    }
}

//セレクトボックスチェック
function validSelect($str, $key){
    if(!preg_match("/^[1-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG18;
    }
}

//パスワードのチェックをまとめた
function validPass($str, $key){
    validHalf($str,$key);
    validMinLen($str,$key);
    validMaxLen($str,$key);
}

// DB接続
function dbConnect(){
    $dsn = 'mysql:dbname=my_clothing;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );

    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

//クエリ実行
function queryPost($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);

    if($stmt->execute($data)){
        debug('クエリ成功 $stmtの中身：'.print_r($stmt,true));
        return $stmt;
    }else {
        debug('クエリ失敗　queryPostのSQLエラー'.print_r($stmt->errorInfo(),true));
        global $err_msg;
        $err_msg['common'] = MSG07;
        return 0;
    }
}

//ユーザー情報取得
function getUser($u_id){
    try {
        $dbh = dbConnect();
        $sql = 'SELECT id, name, zip, addr, tel, email, pass, age, pic, delete_flg, login_time, create_at, update_at FROM users WHERE id = :id AND delete_flg = 0';
        $data = array(':id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        debug('取得したユーザー情報 $resultと$dbFormDataの中身は同じ：'.print_r($result,true));
        if($stmt){
            debug('dbFormData成功しました');
            return $result;
        }
    } catch (Exception $e){
        debug('$dbFormData失敗しました');
        error_log('エラー発生：'.$e->getMessage());
        global $err_msg;
        $err_msg['common'] = MSG07;
    }
}


//カテゴリー取得
function getCategory(){
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetchAll();

        // foreach ($result as $row) : //①’’
        //     debug('ここで成功してほしい'.print_r($row,true));
        // endforeach;
        // debug('どんな結果かな'.print_r($row,true));
        // return $result;

        if($stmt){
            debug('getCategory $result：'.print_r($result,true));
            return $result;
        } else {
            return 0;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }

}


//コーデデータにカテゴリーをジョインして取得
function getCodeOne($p_id){
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM code as c LEFT JOIN category as ca ON c.category_id = ca.category_id WHERE code_id = :code_id';
        $data = array(':code_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // foreach ($result as $row) : //①’’
        //     debug('ここで成功してほしい'.print_r($row,true));
        // endforeach;
        // debug('どんな結果かな'.print_r($row,true));
        // return $result;

        if($stmt){
            debug('getCodeOne $resultの中身：'.print_r($result,true));
            return $result;
        } else {
            return 0;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }

}

//自分のコーデデータ取得
function getCode($u_id,$c_id){
    debug('コーデ情報を取得します');
    debug('ユーザーID：'.$u_id);

    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM code WHERE user_id = :u_id AND code_id = :c_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':c_id' => $c_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('getCode $dataの中身：'.print_r($data,true));
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
        
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }

}

function getCodeList($currentMinNum = 1, $category, $sort, $span = 20){
    debug('コーデリストを取得します。');
    //例外処理
    try {
      // DBへ接続
        $dbh = dbConnect();
        // 件数用のSQL文作成
        $sql = 'SELECT code_id FROM code';
        if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
        if(!empty($sort)){
        switch($sort){
            case 1:
            $sql .= ' ORDER BY price ASC';
            break;
            case 2:
            $sql .= ' ORDER BY price DESC';
            break;
        }
        } 
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        $rst['total'] = $stmt->rowCount(); //総レコード数
        $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
        if(!$stmt){
        return false;
        }
        // ページング用のSQL文作成
        $sql = 'SELECT * FROM code';
        if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
        if(!empty($sort)){
        switch($sort){
            case 1:
            $sql .= ' ORDER BY price ASC';
            break;
            case 2:
            $sql .= ' ORDER BY price DESC';
            break;
        }
        } 
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL：'.$sql);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
        // クエリ結果のデータを全レコードを格納
        $rst['data'] = $stmt->fetchAll();
        return $rst;
        }else{
        return false;
        }

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
    }

/// フォーム入力保持
function getFormData($str){
    global $dbFormData;
    // ユーザーデータがある場合
    if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
    //POSTにデータがある場合
    if(isset($method[$str])){
        return sanitize($method[$str]);
    }else{
        //ない場合（基本ありえない）はDBの情報を表示
        return sanitize($dbFormData[$str]);
    }
    }else{
    //POSTにデータがあり、DBの情報と違う場合
    if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
    }else{
        return sanitize($dbFormData[$str]);
    }
    }
    }else{
    if(isset($method[$str])){
    return sanitize($method[$str]);
    }
    }
    }

//画像アップロード
function uploadImg($file, $key){
    // debug('画像アップロードファイル情報：'.$_FILES,true);
    if(isset($file['error']) && is_int($file['error'])) {
        try {
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('ファイルサイズが大きすぎます');
                    default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG,true])) {
                throw new RuntimeException('画像形式が未対応です');
            }

            $path = 'img/'.sha1_file($file['tmp_name']).image_type_to_extension($type);//imgはフォルダーを指定する
            if(!move_uploaded_file($file['tmp_name'],$path)) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }

            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました　ファイルパス：'.$path);
            return $path;

        } catch (RuntimeException $e){
            global $err_msg;
            $err_msg[$key] = $e->getMessage();

        }
    }

}


//メール送信
function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)) {
        mb_language("Japanese");
        mb_internal_encoding(("UTF-8"));

        $result = mb_send_mail($to, $subject, $comment, "FROM:".$from);

        if($result) {
            debug('メールを送信しました');
        } else {
            debug('メールの送信に失敗しました');
        }
    }
}

//認証キー作成
function makeRandKey($length = 8) {
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0; $i < $length; $i++){
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}


// サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES, 'UTF-8');
}


//GETパラメータ付与
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
        if(!in_array($key,$arr_del_key,true)){ //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
        $str .= $key.'='.$val.'&';
        }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
    }
}

//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
    // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
    if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;
    // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum + 1;
    // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
    }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum + 3;
    // 現ページが1の場合は左に何も出さない。右に５個出す。
    }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;
    // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    // それ以外は左に２個出す。
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
        echo '<ul class="pagination-list">';
        if($currentPageNum != 1){
            echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
        }
        for($i = $minPageNum; $i <= $maxPageNum; $i++){
            echo '<li class="list-item ';
            if($currentPageNum == $i ){ echo 'active'; }
            echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
        }
        if($currentPageNum != $maxPageNum && $maxPageNum > 1){
            echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
        }
        echo '</ul>';
    echo '</div>';
    }
?>