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


<h2>Add relation between Movies and Directors</h2>
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

	Director:
	<br>
	<select name = 'director'  style='width:710px; FONT-SIZE:20PT;'>
		<option value=''></option>
	<?php
		
		$sql_director = 'select last_name, first_name, dob from Director';
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
	<input type="submit" name="submit" value="Submit" style='FONT-SIZE:20PT;'>

</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST")
{	
	if($_POST['title'] == null or $_POST['director'] == null)
	{
		if($_POST['title'] == null)
		{
			echo 'Error: You must choose a movie!';
			echo '<br>';
		}
		if($_POST['director'] == null)
		{
			echo 'Error: You must choose a director!';
		}
	}
	else
	{
		$movie = explode('(' , $_POST['title'])[0];
		$director = explode('(', $_POST['director'])[0];
		$first_name = explode(' ', $director)[0];
		$last_name = explode(' ', $director)[1];

		$sql_mid = 'select id from Movie where title = "' .$movie . '"';
		$mid = $mysqli->query($sql_mid)->fetch_assoc()['id']; 
		//echo $mid; 
		$sql_did = 'select id from Director where first_name = "' .$first_name . '" and last_name = "'. $last_name . '"';
		$did = $mysqli->query($sql_did)->fetch_assoc()['id']; 

		$sql = 'insert into MovieDirector (mid, did) values (' . $mid . ',' . $did . ')';
		$mysqli->query($sql);
		echo 'Successfully add the relation between movies and directors!';
	}

	
	

}

?>

</body>
</html>