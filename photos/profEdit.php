<?php

require('function.php');

debug('ここからprofEdit.php');
debugLogStart();

require('auth.php');

$dbFormData = getUser($_SESSION['user_id']);

if(!empty($_POST)){

$name = $_POST['name'];
$tel = $_POST['tel'];
$zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
$addr = $_POST['addr'];
$age = $_POST['age'];
$email = $_POST['email'];
$pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

// バリデーション
    if($dbFormData['name'] !== $name){
        validMaxLen($name, 'name');
    }

    if($dbFormData['tel'] !== $tel){
        validTel($tel, 'tel');
    }

    if($dbFormData['addr'] !== $addr){
        validMaxLen($addr, ('addr'));
    }

    if($dbFormData['zip'] !== $zip){
        validZip($zip, 'zip');
    }

    if($dbFormData['age'] !== $age){
        validHalf($age, 'age');
    }

    if($dbFormData['email'] !== $email){
        validMaxLen($email, 'email');
        validEmailDup($email);
        validRequired($email, 'email');
    }

    if(empty($err_msg)){

        try {

            $dbh = dbConnect();
            $sql = 'UPDATE users SET name = :name, zip = :zip, addr =:addr, tel = :tel, email = :email, age = :age, pic = :pic WHERE id = :id AND delete_flg = 0';
            $data = array(':name' => $name, ':zip' => $zip, ':addr' => $addr, ':tel' => $tel, ':email' => $email, 'age' => $age, ':pic' => $pic, ':id' => $dbFormData['id']);//$dbFormDataは$_SESSION['user_id']でもOK
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                debug('プロフィール更新完了：');
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }else {
                debug('更新失敗');
            }
        } catch (Exception $e){
            error_log('エラー発生：'. $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

}



debug('ここまでprofEdit.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main">

            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data">
                    <h1 class="form-title">Edit Profile</h1>

                    <div class="form-contents">
                            <div class="area-msg">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                            <input type="text" name="name" value="<?php echo getFormData('name'); ?>" placeholder="氏名">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
                            <input type="text" name="zip" value="<?php echo getFormData('zip'); ?>" placeholder="郵便番号　※ハイフンなしで入力してください">
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
                            <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>" placeholder="住所　都道府県">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['addr'])) echo $err_msg['addr']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
                            <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>" placeholder="電話番号　※ハイフンなしで入力してください"">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['tel'])) echo $err_msg['tel']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
                            <input type="number" name="age" min="0" max="200" value="<?php echo getFormData('age'); ?>" placeholder="0">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                            <input type="text" name="email" value="<?php echo getFormData('email'); ?>" placeholder="メールアドレス">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                        </div>

                        <div class="imgDrop-container">
                            <lavel class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err' ?>">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                <input type="file" name="pic" class="input-file">
                                <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" >
                                ドラッグ＆ドロップ
                            </lavel>
                            <div class="area-msg">
                            <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
                            </div>
                        </div>

                        <div class="btn">
                            <input type="submit" value="編　集">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>
    <?php require('footer.php'); ?>