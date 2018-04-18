<!DOCTYPE html>
<html>
<body>

<h1>Movie Database Quesry Interface</h1>

<form action="query.php" method="post">
<!-- <input type="text" name="query"> -->
<textarea rows='6' cols='40' name="query"><?php echo $_POST["query"];?></textarea></br>
<input type="submit" value="Get it done!">
</form>

<h1>Results</h1>

<?php
$query = $_POST["query"];
echo '<h3>Input query is:</h3>'.$query.'<br>';

echo '<h3>The results of the query are:</h3>';

try{
  $mysqli = new mysqli("localhost", "cs143", "", "CS143");
  $result = $mysqli->query($query);
  //$row = $result->fetch_assoc();
  //echo $row;
  //print_r($row);
  if(!$result){
    echo 'Check your syntax!<br>Note that we do not support multiple queries.<br>';
  }
  else{
    //echo '<table>';
?>
<table border='1'>
<?php
    $c = 0;
    while($row = $result->fetch_assoc())
    {
      if($c==0){
        echo '<tr>';
        foreach($row as $field=>$val)
        {
          echo '<td style="font-weight:bold">'.$field.'</td>';
        }
        echo '</tr>';
      }
      echo '<tr>';
      foreach($row as $field=>$val)
      {
        echo '<td>'.$val.'</td>';
      }
      echo '</tr>';
      $c = $c+1;
    }
    //echo '</table>';
?>
</table>
<?php
  }
}catch(Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}

?>

</body>
</html>
