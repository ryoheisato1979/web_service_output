<?php  
require('function.php');

debug('ここからmypage.php');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETパラメータを取得
//----------------------------------
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ
// カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
// パラメータに不正な値が入っているかチェック
if(!is_int($currentPageNum)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:index.php"); //トップページへ
}
// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan); //1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// DBから商品データを取得
$dbCodeData = getCodeList($currentMinNum, $category, $sort);
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
//debug('DBデータ：'.print_r($dbFormData,true));
//debug('カテゴリデータ：'.print_r($dbCategoryData,true));



debug('ここまでmypage.php');
?>
<?php require('head.php'); ?>

<body>
    
<?php require('header.php'); ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width-index">

    <!-- サイドバー -->
    <section id="sidebar">
        <form name="" method="get">
        <h1 class="title">カテゴリー</h1>
        <div class="selectbox">
            <span class="icn_select"></span>
            <select name="c_id" id="">
            <option value="0" <?php if(getFormData('c_id',true) == 0 ){ echo 'selected'; } ?> >選択してください</option>
            <?php
                foreach($dbCategoryData as $key => $val){
            ?>
                <option value="<?php echo $val['category_id'] ?>" <?php if(getFormData('c_id',true) == $val['category_id'] ){ echo 'selected'; } ?> >
                <?php echo $val['category_name']; ?>
                </option>
            <?php
                }
            ?>
            </select>
        </div>
        
        <div class="sort">
        <input type="submit" value="検索">
        </div>
        </form>

    </section>

    <!-- Main -->
    <section id="mypage" >
        <div class="search-title">
        <div class="search-left">
            <span class="total-num"><?php echo sanitize($dbCodeData['total']); ?></span>件見つかりました
        </div>
        <div class="search-right">
            <span class="num"><?php echo (!empty($dbCodeData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbCodeData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbCodeData['total']); ?></span>件中
        </div>
        </div>
        <div class="panel-list">
        <?php
            foreach($dbCodeData['data'] as $key => $val):
        ?>
            <a href="CoordinatesDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['code_id'] : '?p_id='.$val['code_id']; ?>" class="panel">
            <div class="panel-head">
                <img src="<?php echo sanitize($val['pic']); ?>" alt="<?php echo sanitize($val['code_name']); ?>">
            </div>
            <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['code_name']); ?> </p>
            </div>
            </a>
        <?php
            endforeach;
        ?>
        </div>
        
        <?php pagination($currentPageNum, $dbCodeData['total_page']); ?>
        
    </section>

</div>
<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>