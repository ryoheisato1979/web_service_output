<?php 

require('function.php');

debug('ここからpassRemindRecieve.php');
debugLogStart();

if(empty($_SESSION['auth_key'])){
    header("Location:passRemindSend.php");
}

if(!empty($_POST)){
    debug('ポスト送信情報：'.print_r($_POST,true));

    $auth_key = $_POST['token'];

    validRequired($auth_key, 'token');

        if(empty($err_msg)){
            debug('未入力チェックOK');

            validLength($auth_key, 'token');
            validHalf($auth_key, 'token');
        }

        if($auth_key !==  $_SESSION['auth_key']){
            $err_msg['token'] = MSG15;
        }

        if(time() > $_SESSION['auth_key_limit']){
            $err_msg['token'] = MSG16;
        }

        if(empty($err_msg)){
            debug('認証OK');

        $pass = makeRandKey();

        try {
            $dbh = dbConnect();
            $sql = 'UPDATE users SET pass = :pass WHERE email = :email';
            $data = array('email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                debug('クエリ成功');

                $from = 'info@marumaru.com';
                $to = $_SESSION['auth_email'];
                $subject = '[パスワード再発行完了]　｜　My Clothing';
                $comment = <<<EOT
本メールアドレス宛にパスワード再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインしてください。

ログインページ：http://localhost:8888/webukatu/output/my_clothing/login.php

再発行パスワード：{$pass}
※ログイン後、パスワードの変更をお願い致します。

//////////////////////////////////////////////////////////////////
My Chrothingカスタマーセンター
URL：http://localhost:8888/webukatu/output/my_clothing/
E-mail：info@marumaru.com
//////////////////////////////////////////////////////////////////
EOT;

                sendMail($from, $to, $subject, $comment);
                debug('$commentの中身：'.print_r($comment,true));

                session_unset();
                $_SESSION['msg_success'] = SUC03;
                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                header("Location:login.php");
            } else {
                debug('クエリ失敗');
                $err_msg['common'] = MSG07;
            }

        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }

    }
}



// 例外処理
// sql　パスワードを更新する
// $data  email とセッションのemailを。　パスワードとパスワードハッシュを比べる
 
// クエリ成功の場合
// デバッグ　クエリ成功


// メールの中に「$pass」再発行したパスワードを詰める
// メール送信


// セッション削除
// この場合はunsetを使う


// セッションに成功メッセージを詰める 
// デバッグ　セッション変数の中身
// ログインページへ遷移


// それ以外
// デバッグ　クエリに失敗
// えらーめっせーじ
 
// キャッチ
// エラー発生
// エラーメッセージ表示


debug('ここまでpassRemindRecieve.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">Password Reissue</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['token'])) echo 'err' ?>">
                            認証キーを入力してください<br>
                            <input type="text" name="token" value="" placeholder="認証キー　※8桁の英数字を入力してください">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
                        </div>

                        <div class="btn">
                            <input type="submit" value="送信する">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>