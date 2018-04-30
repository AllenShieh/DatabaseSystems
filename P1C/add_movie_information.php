<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>CS143 DataBase Query System (Add Movie Information)</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="./index.php">Home</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="./search.php">Search<span class="sr-only">(current)</span></a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Show...</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="./show_actor.php">Actor</a>
              <a class="dropdown-item" href="./show_movie.php">Movie</a>
            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add...</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="./add_actor_director.php">Actor/Director</a>
              <a class="dropdown-item" href="./add_movie_information.php">Movie information</a>
              <a class="dropdown-item" href="./add_movie_actor_relation.php">Movie/Actor relation</a>
              <a class="dropdown-item" href="./add_movie_director_relation.php">Movie/Direction relation</a>
              <a class="dropdown-item" href="./add_comments.php">Comments</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main" class="container">
			<div class="starter-template">
				<div class="row">
					<div class="col"></div>
					<div class="col-8" align="left">

            <h1>Add new Movie</h1>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <!-- Title -->
              Title:
              <input class="form-control" name="title" rows="1" cols="100">
              <br>

              <!-- Company -->
              Company:
              <input class="form-control" name="company" rows="1" cols="100">
              <br>

              <!-- Year -->
              Year:
              <select class="form-control" name="year">
              	<option value="NULL">Choose year</option>
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

              <!-- MPAA rating -->
              MPAA Rating:
              <select class="form-control" name="rating">
                <option value="NULL">Choose MPAA rating</option>
            		<option value="G">G</option>
            		<option value="NC-17">NC-17</option>
            		<option value="PG">PG-13</option>
            	 	<option value="R">R</option>
            	 	<option value="surrendere">surrendere</option>
              </select>
              <br>

              <!-- Genre -->
              Genre:
              <input type="checkbox" name="Action" value="Action"> Action
              <input type="checkbox" name="Adult" value="Adult"> Adult
              <input type="checkbox" name="Adventure" value="Adventure"> Adventure
              <input type="checkbox" name="Animation" value="Animation"> Animation
              <input type="checkbox" name="Comedy" value="Comedy"> Comedy
              <input type="checkbox" name="Crime" value="Crime"> Crime
              <input type="checkbox" name="Documentary" value="Documentary"> Documentary
              <input type="checkbox" name="Drama" value="Drama"> Drama
              <input type="checkbox" name="Family" value="Family"> Family
              <input type="checkbox" name="Fantasy" value="Fantasy"> Fantasy
              <input type="checkbox" name="Horror" value="Horror"> Horror
              <input type="checkbox" name="Musical" value="Musical"> Musical
              <input type="checkbox" name="Mystery" value="Mystery"> Mystery
              <input type="checkbox" name="Romance" value="Romance"> Romance
              <input type="checkbox" name="SciFi" value="Sci-Fi"> Sci-Fi
              <input type="checkbox" name="Short" value="Short"> Short
              <input type="checkbox" name="Thriller" value="Thriller"> Thriller
              <input type="checkbox" name="War" value="War"> War
              <input type="checkbox" name="Western" value="Western"> Western
              <br><br>

              <input class="btn btn-primary" type="submit" name="submit" value="Submit">
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

              if($title == null)
              {
                echo 'Title can\'t be empty!';
              }
              elseif($company == null)
              {
                echo 'Company can\'t be empty!';
              }
              else
              {
                $max_id_query = 'update MaxMovieID set id = id + 1';
                $mysqli->query($max_id_query);
                $cur_id_query = 'select id from MaxMovieID';
                $cur_id = $mysqli->query($cur_id_query)->fetch_assoc()['id'];
                $title = '"'. $_POST['title'] . '"';
                $company = '"'.$_POST['company'] . '"';

                // comment this segement can handle year=null bug
                //if($year != null)
                //{
                  //$year = '"' . $year . '"';
                //}

                // user may not choose an MPAA rating
                //if($rating != null)
                //{
                  $rating = '"' . $rating . '"';
                //}
                if($genre != null)
                {
                  $genre = '"' . $genre . '"';
                }
                $sql_movie = 'insert into Movie (id, title, year, rating, company) values (' . ' ' . $cur_id . ',' . $title . ','. $year . ',' . $rating . ',' . $company . ');';
                //echo $sql_movie."<br>";
                //$sql_genere = 'insert into MovieGenre (mid, genre) values (' . ' '. $cur_id . ',' $genre .')';
                $mysqli->query($sql_movie);
                for($i = 0; $i <19; $i++)
                {
                  if( $_POST[$g[$i]] != null)
                  {
                    $sql_genere = 'insert into MovieGenre (mid, genre) values (' . ' ' . $cur_id . ',' . '"'.$_POST[$g[$i]] . '"'.')';
                    $mysqli->query($sql_genere);
                    //echo $sql_genere."<br>";
                  //echo '<br>';
                  }
                }

                echo 'Successfully add the movie information!';
              }
              //echo $sql_movie;
            }
            ?>
          </div>
          <div class="col"></div>
        </div>
      </div>

    </main><!-- /.container -->

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>

  </body>
</html>
