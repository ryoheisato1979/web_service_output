    <header class="header">
            <nav class="nav-container nav-flex">
                <div class="nav-title">
                    <h1><a href="index.php">Photo Album</a></h1>
                </div>
                <div class="nav-list">
                    <ul class="nav-flex">
                        <?php if(empty($_SESSION['user_id'])) { ?>
                        <li><a href="signup.php"">新規登録</a></li>
                        <li><a href="login.php">ログイン</a></li>
                        <?php
                        }else{ 
                        ?>
                        <li><a href="mypage.php">マイページ</a></li>
                        <li><a href="logout.php"">ログアウト</a></li>
                        <li><a href="profEdit.php">プロフィール編集</a></li>
                        <li><a href="passEdit.php"">パスワード変更</a></li>
                        <li><a href="newCoordinates.php"">新規登録</a></li>
                        <li><a href="withDrow.php">退会</a></li>

                        <?php
                        }?>
                    </ul>
                </div>
            </nav>
    </header>