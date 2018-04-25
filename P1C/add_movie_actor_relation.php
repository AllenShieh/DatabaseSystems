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


<h2>Add relation between Movies and Actors</h2>
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

	Actor:
	<br>
	<select name = 'actor'  style='width:710px; FONT-SIZE:20PT;'>
		<option value=''></option>
	<?php
		
		$sql_director = 'select last_name, first_name, dob from Actor';
		$result_director = $mysqli->query($sql_director);

		while($row = $result_director->fetch_assoc())
		{
			echo '<option>';
			echo $row['first_name'] . ' '. $row['last_name'] . '(' . $row['dob'] . ')';
			echo '</option>';

		}

    ?>
	</select>
	<br>
	Role(no more than 50 characters): <br> <textarea name="role" rows="1" cols="70"><?php echo $_POST["role"];?></textarea>
	<br>
	<input type="submit" name="submit" value="Submit" style='FONT-SIZE:20PT;'>

</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST")
{	
	if($_POST['title'] == null or $_POST['actor'] == null or $_POST['role'] == null)
	{
		if($_POST['title'] == null)
		{
			echo 'Error: You must choose a movie!';
			echo '<br>';
		}
		if($_POST['actor'] == null)
		{
			echo 'Error: You must choose a actor!';
		}
		if($_POST['role'] == null)
		{
			echo 'Error: You must specify the role of the actor!';
		}

	}
	else
	{
		$role = $_POST['role'];

		if(strlen($role) > 20)
		{
			echo 'Please enter the role within 20 characters!';
		}
		else 
		{
			$role = '"' . $role . '"';
			$movie = explode('(' , $_POST['title'])[0];
			$actor = explode('(', $_POST['actor'])[0];
			$first_name = explode(' ', $actor)[0];
			$last_name = explode(' ', $actor)[1];


			$sql_mid = 'select id from Movie where title = "' .$movie . '"';
			$mid = $mysqli->query($sql_mid)->fetch_assoc()['id']; 
			//echo $mid; 
			$sql_aid = 'select id from Actor where first_name = "' .$first_name . '" and last_name = "'. $last_name . '"';
			$aid = $mysqli->query($sql_aid)->fetch_assoc()['id']; 

			$sql = 'insert into MovieActor (mid, aid, role) values (' . $mid . ',' . $aid . ',' . $role .')';
			$mysqli->query($sql);
			echo 'Successfully add the relation between movies and directors!';
		}
		
	}

	
	

}

?>

</body>
</html>