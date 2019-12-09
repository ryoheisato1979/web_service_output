<?php 

require('function.php');
debug('ここからwithDrow.php');
debugLogStart();

if($_POST){
    debug('POST送信あります'.print_r($_POST,true));

    try {
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
        $data = array(':u_id' => $_SESSION['user_id']);

        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('index.phpへ遷移します');
            header("Location:index.php");
        } else {
            debug('クエリが失敗しました');
            $err_msg['common'] = MSG07;
        }

    } catch (Exception $e){
        error_log('エラー発生：'. $e->getMessage());
    }
}

debug('ここまでwidhDrow.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form">
                    <h1 class="form-title">WithDrow</h1>

                    <div class="form-contents">

                        <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <div class="btn">
                            <input type="submit" value="退会する" name="submit">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>