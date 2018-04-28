<!--
Need to work on how to process the search

-->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>CS143 DataBase Query System (Search)</title>

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
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Show...</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="./show_actor.php">Actor</a>
              <a class="dropdown-item" href="./show_movie.php">Movie</a>
            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add...</a>
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
        <h1>Search your favourite actor or movie!</h1>
        <p class="lead">Please input his/her name or the movie's title.</p>

        <form action="./search.php" method="post">
          <div class="form-group">
            <input class="form-control" name="searchinput" rows="1" placeholder="name of actor, title of movie..."><br>
            <input class="btn btn-primary" type="submit" value="Search">
          </div>
        </form>

        <?php

        //initialization
        $db_host = 'localhost';
        $db_name = 'CS143';
        $db_user = 'cs143';
        $db_pwd = '';
        $mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);

        #$sql = "select * from Actor";

        if ($_SERVER["REQUEST_METHOD"] == "POST"){
        	$sql = $_POST['searchinput'];
        	$result = $mysqli->query($sql);
        	if($result === false){
        	  echo "Your input is: ".$_POST["searchinput"]."<br>Oops! We got nothing for you.";
        	}
        	else
        	{
            echo "Your input is: ".$_POST["searchinput"]."<br>Let's see what we get!<br>";
        ?>
        <table class="table table-dark">
        <?php
        		$c = 1;
        		while($row = $result->fetch_assoc()){
      		    if($c==1){
      		    	$c = $c + 1;
      		    	echo '<tr>';
      		    	foreach($row as $x=>$x_value){
      		    		echo '<td style="font-weight:bold">'.$x.'</td>';
      		    	}
      		    	echo '</tr>';
      		    }
      		    echo '<tr>';
      		    foreach($row as $x=>$x_value) {
      	  			echo '<td>'.$x_value.'</td>';
        			}
        			echo '</tr>';
        		}
        ?>
        </table>
        <?php
        	}
        	$mysqli->close();
        	$result->free();
        }
        ?>
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
