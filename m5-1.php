<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
        <?php
        $where_edit_num = "";
        $init_name = "";
        $init_comment = "";
        $init_num = "";
        $init_edit_num = "";
        $init_password = "";
        //パスワードフラグ
        $flag = 0;

        //入力値の関数化
        if(isset($_POST["name"])){
            $name = $_POST["name"];
        }
        if(isset($_POST["comment"])){
            $comment = $_POST["comment"];
        }
        //削除するために使用する文字
        if(isset($_POST["num"])){
            $number_post = $_POST["num"];
        }
        //投稿フォームにどこを編集するか表示される数、ブラウザには表示されない
        if(isset($_POST["where_edit_num"])){
            $edit_num_post = $_POST["where_edit_num"];
        }
        if(isset($_POST["pass"])){
            $pass = $_POST["pass"];
        }
        //編集フォーム用
        if(isset($_POST["edit_num"])){
            $edit_num = $_POST["edit_num"];
        }
        $date = date("Y/m/d H:i:s");

        //データベースへの接続
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザ名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブルを作る
        $sql = "CREATE TABLE IF NOT EXISTS board"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);

        //編集する場所
        if(!empty($edit_num)){
            $id = $edit_num;
            $sql = 'SELECT * FROM board';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if($row['id'] == $id){
                    if($row['pass'] == $pass){
                        $where_edit_num = $row['id'];
                        $init_name = $row['name'];
                        $init_comment = $row['comment'];
                    }else{
                        $flag = 1;
                    }
                }
            }
        }
        //データの更新
        if(!empty($name)){
            //編集無し
            if(empty($edit_num_post)){
                $sql = "INSERT INTO board (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->execute();
                        
            //編集モード
            }else{
                $id = $edit_num_post; //変更する投稿番号
                $sql = 'UPDATE board SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();  
            }
        }
        //削除する機能
        if(!empty($number_post)){
            $id = $number_post;
            $sql = 'SELECT * FROM board';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            $flag = 2;
            foreach($results as $row){
                if($row['pass'] == $pass && $row['id'] == $id){
                    $sql = 'DELETE FROM board WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $flag = 0;
                }
            }
        }

        ?>
    </head>
    <body>
        <form action="" method="post">
            <input type="hidden" name="where_edit_num" size="5" value=<?= $where_edit_num ?>>
            <input type="text" name="name" size="15" placeholder="名前" value=<?= $init_name ?>>
            <input type="text" name="comment" size="15" placeholder="コメント" value=<?= $init_comment?>>
            <input type="text" name="pass" size="15" placeholder="パスワード" value=<?= $init_password ?>>
            <input type="submit" name="submit">
        </form>
        <form action="" method="post">
            <input type="text" name="num" size="15" placeholder="削除したい番号" value=<?= $init_num ?>>
            <input type="text" name="pass" size="15" placeholder="パスワード" value=<?= $init_password ?>>
            <input type="submit" name="submit" value="削除">
        </form>
        <form action="" method="post">
            <input type="text" name="edit_num" size="15" placeholder="編集対象番号" value=<?= $init_edit_num ?>>
            <input type="text" name="pass" size="15" placeholder="パスワード" value=<?= $init_password ?>>
            <input type="submit" name="submit" value="編集">
        </form>
        <br>
        <?php
        //パスワードが違うとき
        if($flag == 1 && !empty($edit_num)){
            echo "<h2>パスワードが違います。編集できません。</h2>";  
        }else if($flag == 2){
            echo "<h2>パスワードが違います。削除できません。</h2>";
        }
        
        //画面表示
        $sql = 'SELECT * FROM board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){
            echo $row['id']. ',';
            echo $row['name']. ',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
            echo '<hr>';
        }

        ?>
    </body>
</html>

