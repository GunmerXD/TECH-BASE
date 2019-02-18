
<?php

	//データベース接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

	//テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS mission4"
	."("
	."id INT,"
	."name char(32),"
	."comment TEXT,"
	."time DATETIME,"
	."secret char(32)"
	.");";
	$stmt = $pdo -> query($sql);

	if(isset($_POST['comment'])){

		//編集モード
		if(empty($_POST['edit_number'] )){

			//通常投稿モード
			$sql = 'SELECT*FROM mission4';
			$stmt = $pdo->query($sql);
			$idcount = $stmt->fetchAll();
			$id = count($idcount)+1;

			//データ入力
			$sql = $pdo -> prepare("INSERT INTO mission4(id,name,comment,time,secret)VALUES(:id,:name,:comment,:time,:secret)");

			$sql -> bindParam(':id',$id,PDO::PARAM_STR);
			$sql -> bindParam(':name',$name,PDO::PARAM_STR);
			$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
			$sql -> bindParam(':time',$time,PDO::PARAM_STR);
			$sql -> bindParam(':secret',$secret,PDO::PARAM_STR);	

			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$time = date("Y-m-d H:i:s");
			$secret = $_POST['secret'];
			
			$sql -> execute();

			echo '投稿内容を受け付けました';

		}else{

			$id = $_POST['edit_number'];
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$secret = $_POST['secret'];
			
			$sql = 'update mission4 set name=:name,comment=:comment,secret=:secret where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name',$name,PDO::PARAM_STR);
			$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
			$stmt->bindParam(':id',$id,PDO::PARAM_INT);
			$stmt->bindParam(':secret',$secret,PDO::PARAM_INT);
			$stmt->execute();

			echo $id.'番目の投稿内容を編集しました';

		}
	}

	//削除機能
	if(isset($_POST['delete'])){

		$delete_id = $_POST['delete'];
		$sql = 'SELECT * FROM mission4 WHERE id=:id';
 
		// SQLステートメントを実行
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id',$delete_id,PDO::PARAM_INT);
 		$stmt->execute();

		// foreach文で配列の中身を一行ずつ出力
		foreach ($stmt as $row) {
			
			//パスワードロック	
			if($_POST['delete_secret'] == $row['secret']){

				//削除処理
				$id = $_POST['delete'];
				$sql = 'delete from mission4 where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();

			}else{

				echo 'ERROR Wrong Password';
			}
		}
	}

	//編集機能
	if(isset($_POST['renew']) || renew != '番号'){

		$renew_id = $_POST['renew'];
		$sql = 'SELECT * FROM mission4 WHERE id=:id';
 
		// SQLステートメントを実行
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id',$renew_id,PDO::PARAM_INT);
 		$stmt->execute();

		// foreach文で配列の中身を一行ずつ出力
		foreach ($stmt as $row) {
			
			//パスワードロック	
			if($_POST['renew_secret'] == $row['secret']){
 
				// 編集先のデータ格納
				$renew_name = $row['name'];
				$renew_comment = $row['comment'];

			}else{
			
				echo 'ERROR Wrong Password';
			}
		}
	}
?>

<html>
	<head>
		<title>mission4-2</title>

		<meta charset="utf-8">

	</head>

	<body>
		<form method="post" action="mission_4-2.php">
			<p>投稿フォーム<br>
			名前：<input type = "text" maxlength = "255" value = "<?php echo $renew_name; ?>" name = "name">
			コメント：<input type = "text" maxlength = "255" value = "<?php echo $renew_comment; ?>" name = "comment">
			パスワード：<input type = "text" maxlength = "255" value = "パスワード" name = "secret"></p>
			<p><input type = "hidden" maxlength = "255" value = "<?php echo $renew_id; ?>" name = "edit_number"></p>
			<p><input type = "submit" value="送信"></p>
		</form>

		<form method='post' action="mission_4-2.php">
			<p>削除機能<br>
			<input type="text" maxlength="255" value="番号" name="delete">
			<input type = "text" maxlength = "255" value = "パスワード" name = "delete_secret"></p>
			<p><input type="submit" value="削除"></p>
		</form>

		<form method='post' action="mission_4-2.php">
			<!--編集先指定-->
			<p>編集機能<br>
			<input type="text" maxlength="255" value="番号" name="renew">
			<input type = "text" maxlength = "255" value = "" name = "renew_secret"></p>
			<p><input type="submit" value="編集"></p>	
		</form>

	</body>

</html>

<?php

	//入力データ表示
	$sql = 'SELECT*FROM mission4';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['time'].',';
		echo $row['secret'].',';
		echo "<br>";
	}

?>