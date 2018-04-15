<!DOCTYPE html>
<html>
<body>

<h1>Movie Database Quesry Interface</h1>

<form action="query.php" method="post">
<!-- <input type="text" name="query"> -->
<textarea rows='6' cols='40' name="query"></textarea></br>
<input type="submit" value="Get it done!">
</form>

<h1>Results</h1>

<?php
$query = $_POST["query"];
echo $query.'<br>';

$mysqli = new mysqli("localhost", "cs143", "", "CS143");
$result = $mysqli->query("SELECT * FROM Movie WHERE id=100");
while($row = $result->fetch_row())
{
  //print_r($row);
  foreach($row as $val)
  {
    echo $val.' ';
  }
}
//$row = $result->fetch_assoc();
//echo htmlentities($row);
//echo $query;

?>

</body>
</html>
