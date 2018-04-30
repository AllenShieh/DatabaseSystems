<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="favicon.ico">

		<title>CS143 DataBase Query System (Add Comment)</title>

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
						<?php
							$db_host = 'localhost';
							$db_name = 'CS143';
							$db_user = 'cs143';
							$db_pwd = '';

							$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
							$identifier = $_GET['identifier'];
							$sql_default = "select title, year from Movie where id=".$identifier.";";
							$result_default = $mysqli->query($sql_default);

						?>


						<h1>Add comments</h1>
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

							<!-- Movie title -->
							Movie Title:
							<select class="form-control" name = 'title'>
								<?php
								if($result_default->num_rows>0){
									$row = $result_default->fetch_assoc();
									$default = $row['title'].'('.$row['year'].')';
									echo "<option>".$default."</option>";
								}
								else{
									echo "<option value=''></option>";

									$sql_movie = 'select id, title, year from Movie';
									$result_movie = $mysqli->query($sql_movie);
									while($row = $result_movie->fetch_assoc())
									{
										echo '<option>';
										echo $row['title'] . '(' . $row['year'] . ')';
										echo '</option>';
									}
								}
							  ?>
							</select>
							<br>

							<!-- Comments -->
							Comments(no more than 500 characters):
							<textarea class="form-control" name="comments" rows="7" cols="100"><?php echo $_POST["role"];?></textarea>
							<br>

							<!-- Ratings -->
							Ratings:
							<select class="form-control" name='ratings'>
								<option value = ''>choose ratings</option>
								<option value = '0'>0</option>
								<option value = '1'>1</option>
								<option value = '2'>2</option>
								<option value = '3'>3</option>
								<option value = '4'>4</option>
								<option value = '5'>5</option>
							</select>
							<br>

							<!-- Commentor name -->
							Your name(optional):
							<input class="form-control" name="name" rows="1" cols="100">
							<br>

							<input class="btn btn-primary" type="submit" name="submit" value="Submit">
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
								$ratings = $_POST['ratings'];
								if(strlen($comments) > 500)
								{
									echo 'Please enter the comments within 500 characters!';
								}
								elseif(strlen($name) > 20)
								{
									echo 'Please enter your name within 20 characters~';
								}
								elseif($ratings == null)
								{
									echo 'Please at least rate the movie~';
								}
								else
								{
									$movie = explode('(' , $_POST['title'])[0];
									
									if($comments != null)
									{
										$comments = '"' . $comments . '"';
									}
									else 
									{
										$comments = 'null';
									}
									if($name != null)
									{
										$name = '"'. $name . '"';
									}
									else
									{
										$name = 'null';
									}
								
									
									$time = time();
									$mysqltime= '"' . date('Y-m-d H:i:s',$time) . '"';

									$sql_mid = 'select id from Movie where title = "' .$movie . '"';
									$mid = $mysqli->query($sql_mid)->fetch_assoc()['id'];
									$sql = 'insert into Review (name, time, mid, rating, comment) values (' . $name . ',' . $mysqltime . ',' . $mid . ',' . $ratings . ',' .$comments . ')';
									$mysqli->query($sql);


									//echo $sql."<br>";


									echo 'Successfully add comments!';
									//echo $mysqltime;
								}
							}
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
