<?php  

require('function.php');

debug('ここからnewCoordinates.php');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================

// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
$c_id = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
$dbFormData = getCode($_SESSION['user_id'],$c_id);

debug('dbCategoryDataの中身：'.print_r($dbCategoryData,true));
debug('$dbFormDataの中身：'.print_r($dbFormData,true));



// POST送信時処理
//================================
if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報：'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));


  //変数にユーザー情報を代入
$code_name = $_POST['code_name'];
$category = $_POST['category_id'];
$price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
$comment = $_POST['comment'];
  //画像をアップロードし、パスを格納
$pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う

    //未入力チェック名前
    validRequired($code_name, 'code_name');
    //未入力チェック　金額
    validRequired($price, 'price');

    if(empty($err_msg)){
        debug('未入力チェックOK');
    //最大文字数チェック名前
    validMaxLen($code_name,'code_name');
    //セレクトボックスチェック　カテゴリ
    validSelect($category, 'category_id');
    //最大文字数チェック　コメント
    validMaxLen($comment, 'comment', 500);
    //半角数字チェック　金額
    validNumber($price, 'price');
    }
    if(empty($err_msg)){
        debug('バリデーションOKです。');

        try {
            $dbh = dbConnect();
            $sql = 'INSERT INTO code (code_name, category_id, comment, pic, user_id, create_at) VALUES(:c_name, :c_id, :comment, :pic, :u_id, :date)';
            $data = array(':c_name' => $code_name, ':c_id' => $category, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            
            $stmt = queryPost($dbh, $sql, $data);
            debug('$stmtの中身：'.print_r($stmt,true));
            if($stmt){
                $_SESSION['msg_success'] = SUC03;
                header("Location:mypage.php");
            } else{
                debug('新規写真登録失敗');
            }
        } catch (Exception $e) {
            error_log('エラー発生：'. $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

}
debug('ここまでnewCoordinates.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">

        <!-- Main -->
        <section id="main" >

            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data">
                    <h1 class="form-title">New Photo Album</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['code_name'])) echo 'err' ?>">
                            <input type="text" name="code_name" value="<?php if(!empty($code_name)) echo $code_name; ?>" placeholder="タイトル">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['code_name'])) echo $err_msg['code_name']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['category_id'])) echo 'err' ?>">
                            <select name="category_id" id="">
                                <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?>>選択してください</option>
                            <?php foreach($dbCategoryData as $key => $val){ ?>
                                <option value="<?php echo $val['category_id']; ?>"<?php if(getFormData('category_id') == $val['category_id'] ){ echo 'selected'; } ?>>
                                    <?php echo $val['category_name']; ?>
                                </option>
                            <?php } ?>
                            </select>
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['comment'])) echo 'err' ?>">
                            <textarea name="comment" cols="64" rows="15" placeholder="コメント"><?php if(!empty($comment)) echo $comment; ?></textarea>
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
                        </div>

                        
                        <div style="overflow:hidden;">
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
                        </div>

                        <div class="btn">
                            <input type="submit" value="登　録">
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>

<?php require('footer.php'); ?>