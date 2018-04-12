<!DOCTYPE html>
<html>
<body>

<h1>Calculator</h1>

<form action="calculator.php" method="post">
<input type="text" name="data">
<input type="submit" value="Calculate">
</form>

<h1>Results</h1>



<?php
$s = $_POST["data"];
//eval('$r = '.$s.';');
//echo $r;

$number = "\d+(.\d+)?";
$operator = "[+\/*-]";
$regexp = "/(\+|-)?".$number."(".$operator.$number.")*/";


# echo $s."<br>";
$s = preg_replace('/\s+/', '', $s);
# echo $s."<br>";

if(preg_match($regexp, $s, $matches))
{
  eval('$r = '.$s.';');
  echo $r;
  # print_r($matches);
}
else{
  echo "Error";
}


?>

</body>
</html>
