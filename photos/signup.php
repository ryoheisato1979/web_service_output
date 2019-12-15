<?php 

require('function.php');

debug('ここからsignup.php');

if(!empty($_POST)){//1

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){
        debug('未入力チェックOK');

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');
    validHalf($pass, 'pass');
    validMatch($pass, $pass_re, 'pass_re');
    validEmailDup($email);

    }


    if(empty($err_msg)){
        debug('バリデーションOK');

        try {
            $dbh = dbConnect();

            $sql = 'INSERT INTO users (email, pass, login_time, create_at) VALUES(:email, :pass, :login_time, :create_at)';
            $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_at' => date('Y-m-d H:i:s'));
            
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                debug('クエリ成功　$dataの中身：'.print_r($data,true));
                header("Location:index.php");
            }
            
        } catch (Exception $e){
            debug('エラー発生箇所001');
            error_log('エラー発生：'. $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

}//1


debug('ここまでsignup.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">新規登録</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                            <?php if(!empty($errmsg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['email'])) echo 'err' ?>">
                            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo sanitize($_POST['email']); ?>" placeholder="メールアドレス">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass'])) echo 'err' ?>">
                            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo sanitize($_POST['pass']); ?>" placeholder="パスワード ※6文字以上">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass_re'])) echo 'err' ?>">
                            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo sanitize($_POST['pass_re']); ?>" placeholder="パスワード（確認用）">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
                        </div>

                        <div class="btn">
                            <input type="submit" value="登　録">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>