<!DOCTYPE html>
<html>
<body>

<h2>Add new Movie</h2>



<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   Title: <br> <textarea name="title" rows="1" cols="100" STYLE="FONT-SIZE:20PT"><?php echo $_POST["title"];?></textarea> 
   <br>
   Company: <br> <textarea name="company" rows="1" cols="100" STYLE="FONT-SIZE:20PT"><?php echo $_POST["company"];?></textarea>
   <br>
   Year:
   <br>
   <select name="year"  style="width:710px; FONT-SIZE:20PT;" >  
    	<option value="">Year</option>  
    
    <?php
    	for($i = 1900; $i < 2019; $i++)
    	{
    		echo '<option>';
    		echo $i;
    		echo '</option>';
    	}
    ?>
   </select>
   <br>
   MPAA Rating:
   <br> 
   <select name="rating" style='width:710px; FONT-SIZE:20PT;' >
   		<option value="G">G</option>
   		<option value="NC-17">NC-17</option>
   		<option value="PG">PG-13</option>
   	 	<option value="R">R</option>
   	 	<option value="surrendere">surrendere</option>
   </select>

   <br>
   Genre:
   <br>
   <input type="checkbox" name="Action" value="Action"> Action     
   <input type="checkbox" name="Adult" value="Adult"> Adult 
   <input type="checkbox" name="Adventure" value="Adventure"> Adventure 
   <input type="checkbox" name="Animation" value="Animation"> Animation 
   <br>
   <input type="checkbox" name="Comedy" value="Comedy"> Comedy     
   <input type="checkbox" name="Crime" value="Crime"> Crime 
   <input type="checkbox" name="Documentary" value="Documentary"> Documentary 
   <input type="checkbox" name="Drama" value="Drama"> Drama 
   <br>
   <input type="checkbox" name="Family" value="Family"> Family     
   <input type="checkbox" name="Fantasy" value="Fantasy"> Fantasy 
   <input type="checkbox" name="Horror" value="Horror"> Horror 
   <input type="checkbox" name="Musical" value="Musical"> Musical 
   <br>
   <input type="checkbox" name="Mystery" value="Mystery"> Mystery     
   <input type="checkbox" name="Romance" value="Romance"> Romance 
   <input type="checkbox" name="SciFi" value="Sci-Fi"> Sci-Fi 
   <input type="checkbox" name="Short" value="Short"> Short 
   <br>
   <input type="checkbox" name="Thriller" value="Thriller"> Thriller     
   <input type="checkbox" name="War" value="War"> War 
   <input type="checkbox" name="Western" value="Western"> Western 
   
   <br>
   <input type="submit" name="submit" value="Submit" STYLE="FONT-SIZE:20PT"> 
</form>

<?php
//initialization
$db_host = 'localhost';
$db_name = 'CS143';
$db_user = 'cs143';
$db_pwd = '';

$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $title = $_POST['title'];
  $company = $_POST['company'];
  $year = $_POST['year'];
  $rating = $_POST['rating'];
  $g = array('Action', 'Adult', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'SciFi', 'Short', 'Thriller', 'War', 'Western');

  if($company == null)
  {
    echo 'Company can\'t be empty!';
  }
  elseif($title == null)
  {
    echo 'Title can\'t be empty!';
  }
  else
  {
    $max_id_query = 'select max(id) as id from Movie';
    $max_id_result = $mysqli->query($max_id_query);
    while($row = $max_id_result->fetch_assoc()) {
      $max_id = $row["id"];
      $cur_id = (String)((int)$max_id + 1);
    }
    $title = '"'. $_POST['title'] . '"';
    $company = '"'.$_POST['company'] . '"';

    if($year != null)
    {
      $year = '"' . $year . '"';
    }
    if($rating != null)
    {
      $rating = '"' . $rating . '"';
    }
    if($genre != null)
    {
      $genre = '"' . $genre . '"';
    }
    $sql_movie = 'insert into Movie (id, title, year, rating, company) values (' . ' ' . $cur_id . ',' . $title . ','. $year . ',' . $rating . ',' . $company . ')';
    //$sql_genere = 'insert into MovieGenre (mid, genre) values (' . ' '. $cur_id . ',' $genre .')';
    $mysqli->query($sql_movie);
    for($i = 0; $i <19; $i++)
    {
      if( $_POST[$g[$i]] != null)
      {
        $sql_genere = 'insert into MovieGenre (mid, genre) values (' . ' ' . $cur_id . ',' . '"'.$_POST[$g[$i]] . '"'.')';
        $mysqli->query($sql_genere);
      //echo $sql_genere;
      //echo '<br>';
      }
    }
  }

  
  echo 'Successfully add the movie information!';
  //echo $sql_movie;
}
?>



</body>
</html>