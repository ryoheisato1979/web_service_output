<?php  

require('function.php');
debug('ここからcodeDetail.php');
debugLogStart();

require('auth.php');

$dbCategoryData = getCategory();
$p_id = $_GET['p_id'];
$dbCode = getCode($_SESSION['user_id'], $p_id);
$dbCodeOne = getCodeOne($p_id);

debug('ここまでcoordinetesDetail.php');
?>

<?php require('head.php'); ?>

<body>
    
    <?php require('header.php'); ?>

    <div id="contents" class="site-width">
        <div class="form-container" id="code-detail">
        <!-- Main -->

            <div class="edit-container">
                <div class="edit1">
                    <div class="edit-photo">
                        <img src="<?php  echo $dbCodeOne['pic']; ?>">
                    
                    </div>
                    <div class="edit-name">
                        <p><br><br><br>Photo_Title<br><br><span><?php  echo $dbCodeOne['code_name']; ?></span></p>
                        
                    </div>
                </div>
                
                <div class="edit2">
                    <div class="edit2-1">
                        <p>Category　　:　　<span><?php echo $dbCodeOne['category_name']; ?></span></p>    
                    </div>
                    <div class="edit2-2">
                        <p>Comment　　:　　</p>
                        <p class="p-com"><?php echo $dbCodeOne['comment']; ?></p>   
                    </div>
                </div>

                <div class="edit3">
                        <p>編集は<a href="coordinatesEdit.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'' : '?p_id='.$val['code_id']; ?>">コチラ</a></p>
                </div>
            </div>
        </div>
    </div>


    

    <?php require('footer.php'); ?>