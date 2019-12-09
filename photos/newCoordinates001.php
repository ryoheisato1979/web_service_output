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
// GETデータを格納
$cd_id = (!empty($_GET['cd_id'])) ? $_GET['cd_id'] : '';
//コーデデータを取得する：GETパラメータ改ざん対策に使う
$dbFormData = (!empty($cd_id)) ? getCode($_SESSION['user_id'], $cd_id) : '';
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();

debug('dbFormDataの中身：'.print_r($dbFormData,true));
debug('dbCategoryDataの中身：'.print_r($dbCategoryData,true));


// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($cd_id) && empty($dbFormData)){
    header('Location:mypage.php');
}

// POST送信時処理
//================================


  //変数にユーザー情報を代入
$code_name = $_POST['code_name'];
$category = $_POST['category_id'];
$price = (!empty($price)) ? $_POST['price'] : 0;
$comment = $_POST['comment'];
  //画像をアップロードし、パスを格納
$pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
$pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
    if(empty($dbFormData)){
    //未入力チェック名前
    validRequired($code_name, 'code_name');
    //最大文字数チェック名前
    validMaxLen($code_name,'code_name');
    //セレクトボックスチェック　カテゴリ
    validSelect($category, 'category_id');
    //最大文字数チェック　コメント
    validMaxLen($comment, 'comment', 500);
    //未入力チェック　金額
    validRequired($price, 'price');
    //半角数字チェック　金額
    validHalf($price, 'price');
    }
    if(empty($err_msg)){
        debug('バリデーションOKです。');

        try {
            $dbh = dbConnect();
            $sql = 'INSERT INTO code (code_name, category_id, comment, pic, user_id, create_at) VALUES(:c_name, :c_id, :comment, :pic, :u_id, :date)';
            $data = array(':c_name' => $code_name, ':c_id' => $category, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            
            debug('データの中身：'.print_r($data,true));

            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC03;
                header("Location:mypage.php");
            }
        } catch (Exception $e) {
            error_log('エラー発生：'. $e->getMessage());
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
                    <h1 class="form-title">コーデ登録</h1>

                    <div class="form-contents">
                        <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['code_name'])) echo 'err' ?>">
                            <input type="text" name="code_name" value="" placeholder="コーデ名">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['code_name'])) echo $err_msg['code_name']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['category_id'])) echo 'err' ?>">
                            <select name="category_id" id="">
                            <?php foreach($dbCategoryData as $key => $val){ ?>
                                <option value="<?php echo $val['category_id']; ?>">
                                    <?php echo $val['category_name']; ?>
                                </option>
                            <?php } ?>
                            </select>
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['comment'])) echo 'err' ?>">
                            <textarea name="comment" cols="64" rows="10" value="" placeholder="コーデの特徴"></textarea>
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['price'])) echo 'err' ?>">
                            <input type="text" name="price" value="" placeholder="金額">
                        </lavel>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['price'])) echo $err_msg['price']; ?>
                        </div>

                        <lavel class="<?php if(!empty($err_msg['pic'])) echo 'err' ?>">
                            <input type="file" style="height:200px;width:200px;" name="pic" value="">
                        </lavel>
                        <div class="area-msg">
                        <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
                        </div>


                        <div class="btn">
                            <input type="submit" value="登　録">
                        </div>
                    </div>
                </form>

            </div>
        </section>
        <?php require('sidebar.php'); ?>
    </div>