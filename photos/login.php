<?php

require('function.php');

debug('ここからlogin.php');
debugLogStart();

require('auth.php');
debug('ログイン認証を確認します。');

if(!empty($_POST)){
    debug('POST送信があります。　E-mail：'.print_r($_POST['email'],true));


    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    validRequired($email, 'email');
    validRequired($pass, 'pass');

    if(empty($err_msg)){
        debug('バリデーションOKです。');

        try {
            $dbh = dbConnect();

            $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);

            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!empty($result) && password_verify($pass, $result['pass'])){//array_shift($result)でも良い
                debug('パスワードがマッチしました');
                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();
                if($pass_save){
                    debug('ログイン保持にチェックがあります');
                    $_SESSION['login_limit'] = $sesLimit*24*30;
                } else {
                    debug('ログイン保持にチェックはありません');
                    $_SESSION['login_limit'] = $sesLimit;
                }

                $_SESSION['user_id'] = $result['id'];
                debug('セッションの中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移します');
                header("Location:mypage.php");

            } else {
                debug('パスワードがアンマッチです');
                $err_msg['common'] = MSG09;
            }

        } catch (Exception $e){
            error_log('エラー発生：'. $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

}





debug('ここまでlogin.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">ログイン</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" placeholder="メールアドレス">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['email']))  echo $err_msg['email'];?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>" placeholder="パスワード ※6文字以上">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['pass']))  echo $err_msg['pass'];?>
                        </div>

                        <label>
                            <input type="checkbox" name="pass_save">　次回ログインを省略する
                        </label>

                        <p>パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a></p>

                        <div class="btn">
                            <input type="submit" value="ログイン">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>