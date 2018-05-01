<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="favicon.ico">

		<title>CS143 DataBase Query System (Add Movie Actor Relation)</title>

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

		<?php
			$db_host = 'localhost';
			$db_name = 'CS143';
			$db_user = 'cs143';
			$db_pwd = '';

			$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);

			$sql_movie = 'select id, title, year from Movie';
			$result_movie = $mysqli->query($sql_movie);

			$sql_actor = 'select last_name, first_name, dob from Actor';
			$result_actor = $mysqli->query($sql_actor);
		?>

		<main role="main" class="container">
			<div class="starter-template">
				<div class="row">
					<div class="col"></div>
					<div class="col-8" align="left">

						<h1>Add relation between Movies and Actors</h1>
						<p> Due to the huge amount of Actors, the loading time of this page will be approximate 4s.</p> 
						<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

							<!-- Movie title -->
							Movie Title:
							<select class="form-control" name = 'title'>
								<option value=''></option>
							<?php


							while($row = $result_movie->fetch_assoc())
							{
								echo '<option>';
								echo $row['title'] . '(' . $row['year'] . ')';
								echo '</option>';

							}
							?>
							</select>
							<br>

							<!-- Actor -->
							Actor:
							<select class="form-control" name = 'actor'>
								<option value=''></option>
							<?php


							while($row = $result_actor->fetch_assoc())
							{
								echo '<option>';
								echo $row['first_name'] . ' '. $row['last_name'] . '(' . $row['dob'] . ')';
								echo '</option>';
							}
						  ?>
							</select>
							<br>

							<!-- Role -->
							Role(no more than 50 characters):
							<input class="form-control" name="role" rows="1" cols="70">
							<br>

							<input class="btn btn-primary" type="submit" name="submit" value="Submit">
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
									echo '<br>';
								}
								if($_POST['role'] == null)
								{
									echo 'Error: You must specify the role of the actor!';
									echo '<br>';
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
