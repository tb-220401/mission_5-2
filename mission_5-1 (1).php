<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>
    <body>
      <?php
         
         //DB接続設定
          $dsn='データベース名';
          $user='ユーザー名';
          $password='パスワード';
          $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING)); //DBサーバーとの接続の確立
         
         //テーブル作成
         $sql = "CREATE TABLE IF NOT EXISTS board"
         	     ." ("
                 . "id INT AUTO_INCREMENT PRIMARY KEY,"
          	     . "name char(32),"
         	     . "comment TEXT,"
         	     . "password TEXT,"
         	     . "date DATETIME"
          	     .");";
         $stmt = $pdo->query($sql);
         
         //POST受信
          $name=$_POST["NAME"];
          $comment=$_POST["COMMENT"];
          $pp=$_POST["PP"];
          $dnum=$_POST["D-NUMBER"];
          $dp=$_POST["D-PASS"];
          $enum=$_POST["E-NUMBER"];
          $ep=$_POST["E-PASS"];
          $cnum=$_POST["C-NUMBER"];
          $date=date("Y/m/d/ H:i:s");
          
         //編集内容のフォームへの表示
          $sql2='SELECT * FROM board WHERE id=:enum';
          $stmt2= $pdo->prepare($sql2);
          $stmt2->bindParam(':enum', $enum, PDO::PARAM_INT);
          $stmt2->execute();
          $result2= $stmt2->fetchAll();
            foreach($result2 as $row2){
                if($row2['password']==$ep){
                    $NAME1=$row2['name'];
                    $COMMENT1=$row2['comment'];
                    $CNUM1=$row2['id'];
                }else{}
            }
       ?>    
        
      <form action="" method="post">
            【 投稿フォーム 】<br>
            <input type="txt" name="NAME" placeholder="名前" value="<?php echo $NAME1; ?>"><br>
            <input type="txt" name="COMMENT" placeholder="コメント" value="<?php echo $COMMENT1; ?>"><br>
            <input type="txt" name="PP" placeholder="パスワード">
            <input type="hidden" name="C-NUMBER" value="<?php echo $CNUM1;?>"><br>   
            <input type="submit" name="submit" value="送信"><br><br>
            
            【 削除フォーム 】<br>
            <input type="number" name="D-NUMBER" placeholder="投稿番号"><br>
            <input type="txt" name="D-PASS" placeholder="パスワード"><br>
            <input type="submit" name="submit" value="送信"><br><br>
            
            【 編集フォーム 】<br>
            <input type="number" name="E-NUMBER" placeholder="投稿番号"><br>
            <input type="txt" name="E-PASS" placeholder="パスワード"><br>
            <input type="submit" name="submit" value="送信"><br><br>
        </form>
        
        <?php
          //削除
          $sql3='SELECT * FROM board WHERE id=:dnum';
          $stmt3= $pdo->prepare($sql3);
          $stmt3->bindParam(':dnum', $dnum, PDO::PARAM_INT);
          $stmt3->execute();
          $result3= $stmt3->fetchAll();
            foreach($result3 as $row3){
               if($row3['password']==$dp){
                   $sql4 = 'DELETE FROM board WHERE id=:dnum4';
                   $stmt4 = $pdo->prepare($sql4);
                   $stmt4->bindParam(':dnum4', $dnum, PDO::PARAM_INT);
                   $stmt4->execute();
               }else{}
            }
            
          //新規投稿(確認番号($cnum)があれば編集、なければ追記)
          if(empty($name)){}elseif(empty($comment)){}elseif(empty($pp)){}
          elseif(empty($cnum) && !empty($name) && !empty($comment) && !empty($pp)){  //追記
                $sql5 = $pdo -> prepare("INSERT INTO board (name, comment, password,date) VALUES (:name, :comment, :password, :date)");
             	$sql5 -> bindParam(':name', $name, PDO::PARAM_STR);
             	$sql5 -> bindParam(':comment', $comment, PDO::PARAM_STR);
             	$sql5 -> bindParam(':password',$pp, PDO::PARAM_STR);
             	$sql5 -> bindParam(':date',$date,PDO::PARAM_STR);
             	$sql5 -> execute();
          }elseif(!empty($cnum) && !empty($name) && !empty($comment) && !empty($pp)){  //編集
             	$sql6 = 'UPDATE board SET id=:id,name=:name,comment=:comment,password=:password,date=:date WHERE id=:cnum';
             	$stmt6 = $pdo->prepare($sql6);
              	$stmt6->bindParam(':cnum', $cnum, PDO::PARAM_STR);
              	$stmt6->bindParam('name', $name,PDO::PARAM_STR);
             	$stmt6->bindParam(':comment', $comment, PDO::PARAM_STR);
             	$stmt6->bindParam(':id', $cnum, PDO::PARAM_INT);
             	$stmt6->bindParam(':password',$pp,PDO::PARAM_STR);
             	$stmt6->bindParam(':date',$date,PDO::PARAM_STR);
              	$stmt6->execute();
          }
          
          
          //エラーメッセージ（2つ以上の空欄の時は何もしなくていいか）
                    if(empty($name) && !empty($comment)){
              echo "!!名前が空欄です!!<br>";
          }elseif(!empty($name) && empty($comment)){
              echo "!!コメントが空欄です!!<br>";
          }elseif(!empty($name.$comment) && empty($pp)){
              echo "!!パスワードが空欄です!!<br>";
          }
          
          if(empty($dnum) && !empty($dp)){
              echo "!!削除対象番号が空欄です!!<br>";
          }elseif(!empty($dnum) && empty($dp)){
              echo "!!削除パスワードが空欄です!!<br>";
          }
          
          if(empty($enum) && !empty($ep)){
              echo "!!編集対象番号が空欄です!!<br>";
          }elseif(!empty($enum) && empty($ep)){
              echo "!!編集パスワードが空欄です!!<br>";
          }
          
          //次回、ここから下にパスワードが異なります。のエラーメッセージの設定
          if(!empty($dnum) && !empty($dp)){
          $sql7='SELECT * FROM board WHERE id=:dnum';
          $stmt7= $pdo->prepare($sql7);
          $stmt7->bindParam(':dnum', $dnum, PDO::PARAM_INT);
          $stmt7->execute();
          $result7= $stmt7->fetchAll();
            foreach($result7 as $row7){
                if($row7['password']!==$dp){
                    echo "!!削除パスワードが異なります!!"."<br>";
                }else{}
            }

          }
          
          if(!empty($enum) && !empty($ep)){
          $sql8='SELECT * FROM board WHERE id=:enum';
          $stmt8= $pdo->prepare($sql8);
          $stmt8->bindParam(':enum', $enum, PDO::PARAM_INT);
          $stmt8->execute();
          $result8= $stmt8->fetchAll();
            foreach($result8 as $row8){
                if($row8['password']!==$ep){
                    echo "!!編集パスワードが異なります!!"."<br>";
                }else{}
            }

          }

        ?>
        
        ________________________<br>
        【 投稿一覧 】<br><br>
       
          
         <?php 
         //投稿一覧の表示
         $sql = 'SELECT * FROM board';
         $stmt = $pdo->query($sql);
         $results = $stmt->fetchAll();
         	foreach ($results as $row){
         	 //$rowの中にはテーブルのカラム名が入る
         		echo $row['id'].',';
         		echo $row['name'].',';
         		echo $row['comment'].',';
         		echo $row['password'].',';
         		echo $row['date'].'<br>';
          	echo "<hr>";
         	}
        ?>
        
    </body>
</html>