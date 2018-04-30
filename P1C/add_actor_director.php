<!--
Still need to implement the Director part

-->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>CS143 DataBase Query System (Add Actor/Director)</title>

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
          <h1>Add new director/actor</h1><br>

          <form name="reg_testdate" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            <!-- Actor/Director checkbox -->
            <input type="radio" name="AorD"
          	  <?php if (isset($AorD) && $AorD=="Actor") echo "checked";?>
          	value="Actor" onclick="actor_sex()">Actor
          	<input type="radio" name="AorD"
          		<?php if (isset($AorD) && $AorD=="Director") echo "checked";?>
          	value="Director" onclick="direcctor_sex()">Director
          	<br><br>

            <!-- First name and last name -->
          	First Name:
            <input class="form-control" name="first_name" rows="1" cols="70">
          	<br>
          	Last Name:
            <input class="form-control" name="last_name" rows="1" cols="70">
            <br>

            <!-- Gender -->
            <input type="radio" id = "gender" name="gender"
          		<?php if (isset($gender) && $gender=="Female") echo "checked";?>
          	value="Female">Female
          	<input type="radio" id = "gender"  name="gender"
          		<?php if (isset($gender) && $gender=="Male") echo "checked";?>
          	value="Male">Male
          	<br><br>

            <!-- Date of birth -->
          	Date of Birth : <br>
            <div class="row">
              <div class="col">
              	<select class="form-control" name="DobYYYY" onchange="YYYYDD(this.value)">
                	<option value="">Year</option>
              	</select>
              </div>
              <div class="col">
              	<select class="form-control" name="DobMM" onchange="MMDD(this.value)">
                	<option value="">Month</option>
              	</select>
              </div>
              <div class="col">
              	<select class="form-control" name="DobDD">
              		<option value="">Day</option>
              	</select>
              </div>
            </div>
          	<br>

            <!-- Date of death -->
          	Date of Die (leave blank if alive now): <br>
            <div class="row">
              <div class="col">
              	<select class="form-control" name="DodYYYY" onchange="YYYYDD_dod(this.value)">
              		<option value="">Year</option>
              	</select>
              </div>
              <div class="col">
              	<select class="form-control" name="DodMM" onchange="MMDD_dod(this.value)">
              		<option value="">Month</option>
              	</select>
              </div>
              <div class="col">
              	<select class="form-control" name="DodDD">
              		<option value="">Day</option>
              	</select>
              </div>
            </div>
          	<br>

            <input class="btn btn-primary" type="submit" name="submit" value="Submit">
          </form>


          <?php
          //initialization
          $db_host = 'localhost';
          $db_name = 'CS143';
          $db_user = 'cs143';
          $db_pwd = '';
          $mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);

          //queries
          //$sql = "select * from Actor";

          if ($_SERVER["REQUEST_METHOD"] == "POST")
          {
          	$AorD = $_POST['AorD'];

          	//echo $AorD;
          	$first_name = $_POST['first_name'];
          	$last_name = $_POST['last_name'];
          	$gender = $_POST['gender'];
          	$dob_yy = $_POST['DobYYYY'];
          	$dob_mm = $_POST['DobMM'];
          	$dob_dd = $_POST['DobDD'];
          	$dod_yy = $_POST['DodYYYY'];
          	$dod_mm = $_POST['DodMM'];
          	$dod_dd = $_POST['DodDD'];

          	if(strlen($dob_mm) == 1){
          		$dob_mm = '0' . $dob_mm;
          	}

          	if(strlen($dob_dd) == 1){
          		$dob_dd = '0' . $dob_dd;
          	}

          	if($AorD == null)
          	{
          		echo 'Please choose a category to add!';
          	}
          	elseif ($AorD == 'Actor' and $gender == null) {
          		echo 'please choose a gender!';
          	}
          	elseif ($first_name == null) {
          		echo 'First name is empty!';
          	}
          	elseif ($last_name == null) {
          		echo 'Last name is empty!';
          	}
          	elseif ($dob_yy == null or $dob_mm == null or $dob_dd == null) {
          		echo 'Date of birth are not completed!';
          	}
          	else{
          		$max_id_query = 'update MaxPersonID set id = id + 1';
          		$mysqli->query($max_id_query);
          		$cur_id_query = 'select id from MaxPersonID';
          		$cur_id = $mysqli->query($cur_id_query)->fetch_assoc()['id'];
          		
            	$dob = '"'. $dob_yy . '-' . $dob_mm . '-' . $dob_dd . '"';
            	$last_name = '"'. $last_name . '"';
            	$first_name = '"'. $first_name. '"';
            	$gender = '"'. $gender . '"';
          		if ($dod_yy == null or $dod_mm == null or $dod_dd == null) {
          			if($AorD == 'Actor')
          			{
          				$sql = 'insert into Actor (id, last_name, first_name, sex, dob) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name . ',' . $gender . ',' . $dob . ')';
          			}

          			else
          			{
          				$sql = 'insert into Director (id, last_name, first_name, dob) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name . ','  . $dob . ')';
          			}
          		
          			
          			#echo $sql;
          		}
          		else{
          			if(strlen($dod_mm) == 1){
          				$dod_mm = '0' . $dod_mm;
          			}
          			if(strlen($dod_dd) == 1){
          				$dod_dd = '0' . $dod_dd;
          			}

          			$dod = '"'. $dod_yy . '-' . $dod_mm . '-' . $dod_dd.'"';

          			if($AorD == 'Actor')
          			{
          				$sql = 'insert into Actor (id, last_name, first_name, sex, dob, dod) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name . ',' . $gender . ',' . $dob . ',' .$dod . ')';
          			}
          			else
          			{
          				$sql = 'insert into Director (id, last_name, first_name, dob, dod) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name .  ',' . $dob . ',' .$dod . ')';
          			}
          			

          		}
              #echo $sql."<br>";
              $mysqli->query($sql);
              echo 'Successfully add the information!';
          	}
          }
          ?>
        </div>
        <div class="col"></div>
      </div>
    </div>

  </main><!-- /.container -->

  <script language="JavaScript"><!--
	function direcctor_sex(){
        document.reg_testdate.gender[0].disabled=true;
        document.reg_testdate.gender[1].disabled=true;
      document.reg_testdate.gender.checked=false;
  }

	function actor_sex(){
        document.reg_testdate.gender[0].disabled=false;
        document.reg_testdate.gender[1].disabled=false;
      document.reg_testdate.gender.checked=true;
  }

  //dob
    function YYYYMMDDstart()
    {
      MonHead = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
      //year
      var y  = new Date().getFullYear();
      for (var i = 1900; i < 2018; i++) //range from 1900 to 2017
        document.reg_testdate.DobYYYY.options.add(new Option(" "+ i, i));

      //month
      for (var i = 1; i < 13; i++)
        document.reg_testdate.DobMM.options.add(new Option(" " + i , i));

      //document.reg_testdate.YYYY.value = y;
      //document.reg_testdate.MM.value = new Date().getMonth() + 1;
      var n = MonHead[new Date().getMonth()];
      if (new Date().getMonth() ==1 && IsPinYear(YYYYvalue)) n++;
      writeDay(n);
      //document.reg_testdate.DD.value = new Date().getDate();
    }
    if(document.attachEvent)
      window.attachEvent("onload", YYYYMMDDstart);
    else
      window.addEventListener('load', YYYYMMDDstart, false);
    function YYYYDD(str) //lunar or not
    {
      var MMvalue = document.reg_testdate.DobMM.options[document.reg_testdate.DobMM.selectedIndex].value;
      if (MMvalue == ""){ var e = document.reg_testdate.DobDD; optionsClear(e); return;}
      var n = MonHead[MMvalue - 1];
      if (MMvalue ==2 && IsPinYear(str)) n++;
      writeDay(n);
    }
    function MMDD(str)   //month related day
    {
      var YYYYvalue = document.reg_testdate.DobYYYY.options[document.reg_testdate.DobYYYY.selectedIndex].value;
      if (YYYYvalue == ""){ var e = document.reg_testdate.DobDD; optionsClear(e); return;}
      var n = MonHead[str - 1];
      if (str ==2 && IsPinYear(YYYYvalue)) n++;
      writeDay(n);
    }
    function writeDay(n)
    {
      var e = document.reg_testdate.DobDD; optionsClear(e);
      for (var i=1; i<(n+1); i++)
        e.options.add(new Option(" "+ i, i));
    }
    function IsPinYear(year)//lunar or not
    {
      return(0 == year%4 && (year%100 !=0 || year%400 == 0));
    }
    function optionsClear(e)
    {
      e.options.length = 1;
    }
    // dod
    function YYYYMMDDstart_dod()
    {
      MonHead = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
      //year
      var y  = new Date().getFullYear();
      for (var i = 1900; i < 2018; i++) //range from 1900 to 2017
        document.reg_testdate.DodYYYY.options.add(new Option(" "+ i, i));

      //month
      for (var i = 1; i < 13; i++)
        document.reg_testdate.DodMM.options.add(new Option(" " + i , i));

      //document.reg_testdate.YYYY.value = y;
      //document.reg_testdate.MM.value = new Date().getMonth() + 1;
      var n = MonHead[new Date().getMonth()];
      if (new Date().getMonth() ==1 && IsPinYear(YYYYvalue)) n++;
      writeDay_dod(n);
      //document.reg_testdate.DD.value = new Date().getDate();
    }
    if(document.attachEvent)
       window.attachEvent("onload", YYYYMMDDstart_dod);
    else
       window.addEventListener('load', YYYYMMDDstart_dod, false);
    function YYYYDD_dod(str) //lunar or not
    {
      var MMvalue = document.reg_testdate.DodMM.options[document.reg_testdate.DodMM.selectedIndex].value;
      if (MMvalue == ""){ var e = document.reg_testdate.DodDD; optionsClear(e); return;}
      var n = MonHead[MMvalue - 1];
      if (MMvalue ==2 && IsPinYear(str)) n++;
        writeDay_dod(n);
    }
    function MMDD_dod(str)   //month related day
    {
      var YYYYvalue = document.reg_testdate.DodYYYY.options[document.reg_testdate.DodYYYY.selectedIndex].value;
      if (YYYYvalue == ""){ var e = document.reg_testdate.DodDD; optionsClear(e); return;}
      var n = MonHead[str - 1];
      if (str ==2 && IsPinYear(YYYYvalue)) n++;
      writeDay_dod(n);
    }
    function writeDay_dod(n)
    {
      var e = document.reg_testdate.DodDD; optionsClear(e);
      for (var i=1; i<(n+1); i++)
        e.options.add(new Option(" "+ i, i));
    }
  //--></script>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
  <script src="assets/js/vendor/popper.min.js"></script>
  <script src="dist/js/bootstrap.min.js"></script>

  </body>
</html>
