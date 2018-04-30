<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>CS143 DataBase Query System (Show Movie)</title>

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

          <!--
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>

          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
          -->

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

          <!--
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
          -->
        </ul>

        <!--
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        -->

      </div>
    </nav>

    <main role="main" class="container">

      <div class="starter-template">
        <div class="row">
          <div class="col"></div>
          <div class="col-8">

            <h1>Movie Information</h1>
            <?php
            $identifier = $_GET['identifier'];

            if($identifier!=NULL){
              //initialization
              $db_host = 'localhost';
              $db_name = 'CS143';
              $db_user = 'cs143';
              $db_pwd = '';
              $mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);

              $sql_movie = "select title as Title, year as Year, company as Producer, rating as 'MPAA Rating' from Movie where id=".$identifier.";";
              $sql_movie_director = "select concat(first_name, ' ', last_name) as Name, dob as 'DoB' from Director join MovieDirector on Director.id=MovieDirector.did where mid=".$identifier.";";
              $sql_movie_genre = "select genre as Genre from Movie join MovieGenre on Movie.id=MovieGenre.mid where id=".$identifier.";";

              $result_movie = $mysqli->query($sql_movie);
              $result_movie_director = $mysqli->query($sql_movie_director);
              $result_movie_genre = $mysqli->query($sql_movie_genre);

              echo "<br>";
              echo "<h3 align='left'>Movie's information is:</h3>";
              echo "<table class='table table-sm table-bordered'>";

              while($row = $result_movie->fetch_assoc()){
                foreach($row as $x=>$x_value) {
                  echo "<tr><td style='font-weight:bold'>".$x."</td><td>".$x_value."</td></tr>";
                }
              }

              echo "<tr><td style='font-weight:bold'>Director<td>";
              while($row = $result_movie_director->fetch_assoc()){
                $c = 1;
                foreach($row as $x=>$x_value) {
                  if($c==1){
                    $c = 2;
                    echo $x_value;
                  }
                  else{
                    $c = 1;
                    echo "(".$x_value.")<br>";
                  }
                }
              }
              echo "</td></tr>";

              echo "<tr><td style='font-weight:bold'>Genre<td>";
              $r = 1;
              while($row = $result_movie_genre->fetch_assoc()){
                foreach($row as $x=>$x_value) {
                  if($r>1) echo ", ";
                  echo $x_value;
                  $r = $r+1;
                }
              }
              echo "</td></tr>";

              echo "</table>";

              $sql_actor = "select id, concat(first_name, ' ', last_name) as Name, role as Role from Actor join MovieActor on Actor.id=MovieActor.aid where mid=".$identifier.";";

              $result_actor = $mysqli->query($sql_actor);
              //echo $sql_actor_role;

              echo "<br>";
              echo "<h3 align='left'>Actors in this movie:</h3>";
              echo "<table class='table table-sm table-bordered'>";

              $r = 1;
              while($row = $result_actor->fetch_assoc()){
                if($r==1){
                  $r = 0;
                  echo '<tr>';
                  $c = 1;
                  foreach($row as $x=>$x_value){
                    if($c==1){
                      $c = 0;
                    }
                    else echo '<td style="font-weight:bold">'.$x.'</td>';
                  }
                  echo '</tr>';
                }
                echo '<tr>';
                $c = 1;
                $ref = "./show_actor.php?identifier=";
                foreach($row as $x=>$x_value) {
                  if($c==1){
                    $c = 2;
                    $ref = $ref.$x_value;
                  }
                  elseif($c==2){
                    $c = 0;
                    echo "<td><a href=".$ref.">".$x_value."</a></td>";
                  }
                  else{
                    echo "<td>".$x_value."</td>";
                  }
                }
                echo '</tr>';
              }

              echo "</table>";

              echo "<div align='left'>";
              echo "<br>";
              echo "<h3>User Reviews</h3>";

              $sql_average = "select avg(rating) from Review where mid=".$identifier.";";

              $result_average = $mysqli->query($sql_average);

              while($row = $result_average->fetch_assoc()){
                foreach($row as $x=>$x_value){
                  if($x_value==NULL){
                    echo "There is no review right now. Be the first to comment on this movie!<br>";
                  }
                  else{
                    echo "Current average rating for this movie is ".$x_value."/5<br>";
                  }
                  echo "<a href='./add_comments.php?identifier=".$identifier."'>Write a review</a><br><br>";
                }
              }

              echo "<h3>Reviews Details</h3>";
              $sql_comment = "select name, rating, time, comment from Review where mid=".$identifier.";";
              //echo $sql_comment."<br>";
              $result_comment = $mysqli->query($sql_comment);

              while($row = $result_comment->fetch_assoc()){
                $c = 1;
                foreach($row as $x=>$x_value){
                  if($c==1){
                    if($x_value == null)
                    {
                      $x_value = 'Anonymous';
                    }
                    //echo $x_value;
                    echo "<font class='text-primary'>".$x_value."</font> ";
                  }
                  elseif($c==2){
                    echo "rated this movie with score <font class='text-success'>".$x_value."</font> ";
                  }
                  elseif($c==3){
                    echo "and comment at <font class='text-info'>".$x_value."</font><br>";
                  }
                  else{
                    echo "<font class='text-secondary'>".$x_value."</font><br><br>";
                  }
                  $c = $c+1;
                }
              }

              echo "</div>";
            }
            ?>


            <br>
            <h2>Search your favourite movie!</h2>
            <p class="lead">Please type in its title.</p>

            <form action="./search.php" method="post">
              <div class="form-group">
                <input class="form-control" name="searchinput" rows="1" placeholder="title of movie"><br>
                <input class="btn btn-primary" type="submit" value="Search">
              </div>
            </form>
          </div>
          <div class="col"></div>
        </div>
      </div>

    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>
