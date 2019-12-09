<?php

debug('ログイン認証を行います');

if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');

    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバーです');

        session_destroy();

        header("Location:no_login_msg.php");
    } else {
        debug('有効期限以内です');

        $_SESSION['login_date'] = time();

        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header("Location:mypage.php");
        }

    }
}else {
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:no_login_msg.php");
    }
}

?>