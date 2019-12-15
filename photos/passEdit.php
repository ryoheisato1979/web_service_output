<?php 

require('function.php');

debug('ここからpassEdit.php');
debugLogStart();

$dbUserData = getUser($_SESSION['user_id']);

if(!empty($_POST)){
    debug('ポスト送信の中身：'.print_r($_POST,true));

    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    validRequired($pass_old,'pass_old');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
        debug('未入力チェックOK');

        validPass($pass_old, 'pass_old');
        validPass($pass_new, 'pass_new');

        if(!password_verify($pass_old,$dbUserData['pass'])){
            $err_msg['pass_old'] = MSG12;
        }

        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG13;
        }

        validMatch($pass_new, $pass_new_re, 'pass_new_re');

        if(empty($err_msg)){
            debug('バリデーションOK');

            try {
                $dbh = dbConnect();
                $sql = 'UPDATE users SET pass =:pass WHERE id = :id AND delete_flg = 0';
                $data = array(':pass' => password_hash($pass_new_re,PASSWORD_DEFAULT),':id' => $dbUserData['id']);
                $stmt = queryPost($dbh, $sql, $data);

                if($stmt){
                    debug('パスワード更新完了');
                    $_SESSION['msg_success'] = SUC01;

                    debug('マイページへ遷移します');
                    header("Location:mypage.php");
                }
            } catch (Exception $e){
                error_log('エラー発生：'. $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
debug('ここまでpassEdit.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">Edit Password</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
                            <input type="password" name="pass_old" value="<?php echo $pass_old; ?>" placeholder="古いパスワード">
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
                            <input type="password" name="pass_new" value="<?php echo $pass_new; ?>" placeholder="新しいパスワード　※6文字以上で入力してください">
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
                            <input type="password" name="pass_new_re" value="" placeholder="新しいパスワード（確認用）">
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re']; ?>
                        </div>

                        <div class="btn">
                            <input type="submit" value="変　更">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>

    <?php require('footer.php'); ?>