<!DOCTYPE html>
<html>
<body>
<?php
	$db_host = 'localhost';
	$db_name = 'CS143';
	$db_user = 'cs143';
	$db_pwd = '';

	$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
?>


<h2>Add comments</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
	Movie Title:
	<br>
	<select name = 'title'  style='width:710px; FONT-SIZE:20PT;'>
		<option value=''></option>
	<?php
		
		$sql_movie = 'select id, title, year from Movie';
		$result_movie = $mysqli->query($sql_movie);

		while($row = $result_movie->fetch_assoc())
		{
			echo '<option>';
			echo $row['title'] . '(' . $row['year'] . ')';
			echo '</option>';

		}

    ?>
	</select>

	<br>
	Comments(no more than 500 characters): <br> <textarea name="comments" rows="7" cols="100" style='width:710px; FONT-SIZE:20PT;'><?php echo $_POST["role"];?></textarea>
	<br>
	Ratings:
	<br>
	<select name:'ratings' style='width:710px; FONT-SIZE:20PT;'>
		<option value=''></option>
		<option value = '0'>0</option>
		<option value = '1'>1</option>
		<option value = '2'>2</option>
		<option value = '3'>3</option>
		<option value = '4'>4</option>
		<option value = '5'>5</option>		
	</select>
	<br>
	Your name(optional): <br> <textarea name="name" rows="1" cols="100" style='width:710px; FONT-SIZE:20PT;'><?php echo $_POST["role"];?></textarea> 
	<br>
	<input type="submit" name="submit" value="Submit" style='FONT-SIZE:20PT;'>

</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST")
{	
	if($_POST['title'] == null)
	{		
		echo 'Error: You must choose a movie!';
		echo '<br>';
	}
	else
	{
		$comments = $_POST['comments'];
		$name = $_POST['name'];
		if(strlen($comments) > 500)
		{
			echo 'Please enter the comments within 500 characters!'; 
		}
		elseif(strlen($name) > 20)
		{
			echo 'Please enter your name within 20 characters~';
		}
		else
		{
			$movie = explode('(' , $_POST['title'])[0];
			if($comments != null)
			{
				$comments = '"' . $comments . '"';
			}
			if($name != null)
			{
				$name = '"'. $name . '"';
			}
			
			$ratings = $_POST['ratings'];
			$time = time();
			$mysqltime= '"' . date('Y-m-d H:i:s',$time) . '"';

			$sql_mid = 'select id from Movie where title = "' .$movie . '"';
			$mid = $mysqli->query($sql_mid)->fetch_assoc()['id']; 
			$sql = 'insert into Review (name, time, mid, rating, comment) values (' . $name . ',' . $mysqltime . ',' . $mid . ',' . $ratings . ',' .$comments . ')';
			$mysqli->query($sql);
			echo 'Successfully add comments!';
			//echo $mysqltime;
		}
	}
}
?>
</body>
</html>
