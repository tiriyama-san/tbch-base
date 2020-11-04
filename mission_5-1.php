<!DOCTYPE html>
<?php
    //データベースアクセス
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                    
    //作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	. ");";
	$stmt = $pdo->query($sql);

?>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>
        <h1>掲示板</h1>
        <form action="" method="post">
            <p>
                名前：
                <input type="text" name="name">
            </p>
            <p>
                コメント：
                <input type="text" name="comment">
            </p>
            <p>
                番号(削除・編集では必須)：
                <input type="number" name="num">
            </p>
            <hr>
            <p>
                パスワード：
                <input type="password" name="pass">
            </p>
            <input type="submit" name="send" value="送信">
            <input type="submit" name="remove" value="削除">
            <input type="submit" name="edit" value="編集">
        </form>
        <?php
            //データベースアクセス
            function db(){
                $dsn = 'データベース名';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                return $pdo;
            }
                    
            //入力
	        function write_date($pdo, $new_name,$new_comment,$new_pass){
	            $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, pass) 
	                                    VALUES (:name, :comment, :date, :pass)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $name = $new_name;
                $comment = $new_comment;
                $date = date("Y/m/d H:i:s");
                $pass = $new_pass;
                $sql -> execute();
	        }
	        //編集
	        function edit_date($pdo, $index,$re_name,$re_comment){
	            $id = $index;
	            if(!empty($re_name)){
	                $name = $re_name;
	                $sql = 'UPDATE mission5 SET name = :name WHERE id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':name',$name,PDO::PARAM_STR);
	                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();
	            }
	            if(!empty($re_comment)){
	                $comment = $re_comment;
	                $sql = 'UPDATE mission5 SET comment = :comment WHERE id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
	                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt->execute();
	            }
                $date = date("Y/m/d H:i:s");
                $sql = 'UPDATE mission5 SET date=:date WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':date',$date,PDO::PARAM_STR);
                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                $stmt->execute();
	        }
            //削除
	        function delete_date($pdo, $index){
	            $id = $index;
                $sql = 'delete from mission5 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                $stmt->execute();
	        }
	        //表示
            function display_date($pdo){
                $sql = 'SELECT * FROM mission5';
                $stmt = $pdo->query($sql);
                $results = $stmt -> fetchAll();
                foreach ($results as $row){
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['date']."<br>";
                    echo "<hr>";
                }
            }
            
            //パスワードチェック
            function check_pass($pdo, $index, $pass){
                $sql = 'SELECT * FROM mission5';
                $stmt = $pdo->query($sql);
                $results = $stmt -> fetchAll();
                foreach ($results as $row) {
                    if($row['id'] == $index and $row['pass']==$pass){
                        return true;
                    }
                }
                return false;
            }
            
            //変数
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $num = $_POST["num"];
            $pass = $_POST["pass"];
            $pdo = db();
            
            //投稿
            if(isset($_POST['send'])){
                if(!empty($name) and 
                    !empty($comment) and 
                    !empty($pass)){
                        write_date($pdo,$name,$comment,$pass);
                }
            }
            //削除
            elseif(isset($_POST['remove'])){
                if(check_pass($pdo,$num,$pass)){
                    delete_date($pdo,$num);
                }
            }
            //編集
            elseif(isset($_POST['edit'])){
                if(check_pass($pdo,$num,$pass)){
                    edit_date($pdo,$num,$name,$comment);
                }
            }
            //出力
            echo "<br><br>";
            echo "<hr>";
            display_date($pdo);
        ?>
    </body>
</html>