    <header class="header">
            <nav class="nav-container nav-flex">
                <div class="nav-title">
                    <h1><a href="index.php">Photo Album</a></h1>
                </div>
                <div class="nav-list">
                    <ul class="nav-flex">
                        <?php if(empty($_SESSION['user_id'])) { ?>
                        <li><a href="signup.php"">Sign Up</a></li>
                        <li><a href="login.php">Login</a></li>
                        <?php
                        }else{ 
                        ?>
                        <li><a href="mypage.php">My_Page</a></li>
                        <li><a href="logout.php"">Logout</a></li>
                        <li><a href="profEdit.php">Profile_Edit</a></li>
                        <li><a href="passEdit.php"">Password_Edit</a></li>
                        <li><a href="newCoordinates.php"">Entry Photos</a></li>
                        <li><a href="withDrow.php">Unsubscribe</a></li>

                        <?php
                        }?>
                    </ul>
                </div>
            </nav>
    </header>