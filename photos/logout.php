<?php 

require('function.php');
debug('ここからlogout.php');
debug('ログアウトします');
session_destroy();
debug('セッションの中身：'.print_r($_SESSION,true));
debug('index.phpに遷移します');
header("Location:index.php");

?>