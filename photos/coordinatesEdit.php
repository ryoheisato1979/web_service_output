<?php  

require('function.php');
debug('ここからcoordhinatesEdit.php');
debugLogStart();

require('auth.php');

$dbCategoryData = getCategory();
$p_id = $_GET['p_id'];
$dbFormData = getCode($_SESSION['user_id'], $p_id);
$dbCodeOne = getCodeOne($p_id);

$code_name = $_POST['code_name'];
$category_id = $_POST['category_id'];
$comment = $_POST['comment'];
$pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う;


if(!empty($_POST)){
    try {
        $dbh = dbConnect();
        $sql = 'UPDATE code SET code_name = :c_name, category_id = :c_id, comment = :comment, pic = :pic, update_at = :update_at  WHERE code_id = :p_id AND delete_flg = 0';
        $data = array(':c_name' => $code_name, ':c_id' => $category_id, ':comment' => $comment, ':pic' => $pic, ':update_at' => date('Y-m-d H:i:s'), ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('更新成功');
            debug('マイページへ遷移します');
            header("Location:mypage.php");
        } else {
            debug('更新失敗');
            return false;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

debug('ここまでcoordinatesEdit.php');
?>



<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
            <form action="" method="post" class="form" enctype="multipart/form-data">
                    <h1 class="form-title">Edit Photos</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['code_name'])) echo 'err' ?>">
                            <input type="text" name="code_name" value="<?php echo $dbCodeOne['code_name']; ?>" placeholder="コーデ名">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['code_name'])) echo $err_msg['code_name']; ?>
                        </div>


                        <lavel class="<?php if(!empty($err_msg['category_id'])) echo 'err' ?>">
                            <select name="category_id" id="">
                                <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?>>選択してください</option>
                            <?php foreach($dbCategoryData as $key => $val){ ?>
                                <option value="<?php echo $val['category_id']; ?>"<?php if($dbCodeOne['category_id'] == $val['category_id'] ){ echo 'selected'; } ?>>
                                    <?php echo $val['category_name']; ?>
                                </option>
                            <?php } ?>
                            </select>
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?>
                        </div>

                        <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">

                        <textarea name="comment" id="js-count" cols="64" rows="10" placeholder="コメント"><?php echo $dbCodeOne['comment']; ?></textarea>
                        </label>
                        
                        <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['comment'])) echo $err_msg['comment'];
                        ?>
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