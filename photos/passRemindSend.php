<?php 

require('function.php');

debug('ここからpassRemindSend.php');
debugLogStart();

if(!empty($_POST)){//1
    debug('ポスト送信の中身：'.print_r($_POST,true));

    $email = $_POST['email'];

    validRequired($email, 'email');

    if(empty($err_msg)){
        debug('未入力チェックOK');

        validEmail($email, 'email');
        validMaxLen($email, 'email');
    }

    if(empty($err_msg)){
        debug('バリデーションOK');
        try {

            $dbh = dbConnect();
            $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh, $sql, $data);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt && array_shift($result)){
                debug('クエリ成功 DB登録あり');
                $_SESSION['msg_success'] = SUC03;

                $auth_key = makeRandKey();

                $from = 'info@mrumaru.com';
                $to = $email;
                $subject = '[パスワード再発行認証キー]　｜　My Clothing';
                $comment = <<<EOT
本メールアドレスあてにパスワード再発行のご依頼がありました。
下記のURL日認証キーをご入力いただくと、パスワードが再発行されます。

パスワードの再発行認証キー入力ページ：
http://localhost:8888/webukatu/output/my_clothing/passRemindSendRecieve.php

認証キー：{$auth_key}
※認証キーの有効期限は３０分となります。

認証キーを再発行されたい場合は、下記ページより再度再発行をお願い致します。
http://localhost:8888/webukatu/output/my_clothing/passRemindSend.php

//////////////////////////////////////////////////////////////////
My Chrothingカスタマーセンター
URL：http://localhost:8888/webukatu/output/my_clothing/
E-mail：info@marumaru.com
//////////////////////////////////////////////////////////////////
EOT;
                sendMail($from, $to, $subject, $comment);

                $_SESSION['auth_key'] = $auth_key;
                $_SESSION['auth_email'] = $email;
                $_SESSION['auth_key_limit'] = time()+(60+30);

                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                header("Location:passRemindRecieve.php");

            } else {
                debug('クエリに失敗したか、DBに登録のないEmailが入力されました');
                debug('$resultの中身'.print_r($result,true));
                $err_msg['common'] = MSG07;
            }

        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }


}//1



// 例外処理
// sql　メールを取得する（メールを数える）

// クエリ成功、かつDBに登録されている場合
// array_shift
// デバッグ　クエリ成功
// passRemindRecieveに成功メッセージ　セッションに詰める
// 認証キー生成
// メールを送信
// メールの中に「$auth_key」を詰める


// 認証に必要な情報をセッションに詰める
// $auth_key
// $email
// 有効期限３０分に設定 time()
// デバッグ　セッション変数の中身
// 認証キー入力ページへ遷移


// それ以外
// デバッグ　クエリに失敗したかDBに登録のないメールが入力されました

// キャッチ
// エラー発生
// エラーメッセージ表示


?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">Issue Authentication Key</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['email'])) echo 'err'; ?> ">
                            ご指定のメールアドレスに認証キーを送信します<br>
                            <input type="text" name="email" value="<?php getFormData('email') ?>" placeholder="メールアドレス"">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                        </div>

                        <div class="btn">
                            <input type="submit" value="送信する">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>