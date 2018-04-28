<!--
Still need to implement the Director part

-->
<!DOCTYPE html>
<html>
<body>
<h2>Add new director/actor</h2>

<form name="reg_testdate" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <!-- Actor/Director checkbox -->
  <input type="radio" name="AorD"
	  <?php if (isset($AorD) && $AorD=="Actor") echo "checked";?>
	value="Actor" >Actor
	<input type="radio" name="AorD"
		<?php if (isset($AorD) && $AorD=="Director") echo "checked";?>
	value="Director">Director
	<br>

  <!-- First name and last name -->
	First Name: <br> <textarea name="first_name" rows="1" cols="70" style='width:200px; FONT-SIZE:15PT;' ><?php echo $_POST["first_name"];?></textarea>
	<br>
	Last Name: <br> <textarea name="last_name" rows="1" cols="70" style='width:200px; FONT-SIZE:15PT;' ><?php echo $_POST["last_name"];?></textarea>
  <br>

  <!-- Gender -->
  <input type="radio" name="gender"
		<?php if (isset($gender) && $gender=="Female") echo "checked";?>
	value="Female">Female
	<input type="radio" name="gender"
		<?php if (isset($gender) && $gender=="Male") echo "checked";?>
	value="Male">Male
	<br>

  <!-- Date of birth -->
	Date of Birth : <br>
	<select name="DobYYYY" onchange="YYYYDD(this.value)" style='width:67px; FONT-SIZE:15PT;'>
  	<option value="">Year</option>
	</select>
	<select name="DobMM" onchange="MMDD(this.value)" style='width:67px; FONT-SIZE:15PT;'>
  	<option value="">Month</option>
	</select>
	<select name="DobDD" style='width:67px; FONT-SIZE:15PT;'>
		<option value="">Day</option>
	</select>
	<br>

  <!-- Date of death -->
	Date of Die (leave blank if alive now): <br>
	<select name="DodYYYY" onchange="YYYYDD_dod(this.value)" style='width:67px; FONT-SIZE:15PT;'>
		<option value="">Year</option>
	</select>
	<select name="DodMM" onchange="MMDD_dod(this.value)" style='width:67px; FONT-SIZE:15PT;'>
		<option value="">Month</option>
	</select>
	<select name="DodDD" style='width:67px; FONT-SIZE:15PT;'>
		<option value="">Day</option>
	</select>
	<br>
  <input type="submit" name="submit" value="Submit" style='width:100px; FONT-SIZE:15PT;'>
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
	elseif ($gender == null) {
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
		$max_id_query = 'select max(id) as id from Actor';
		$max_id_result = $mysqli->query($max_id_query);
		while($row = $max_id_result->fetch_assoc()) {
    	$max_id = $row["id"];
    	$cur_id = (String)((int)$max_id + 1);
  	}
  	$dob = '"'. $dob_yy . '-' . $dob_mm . '-' . $dob_dd . '"';
  	$last_name = '"'. $last_name . '"';
  	$first_name = '"'. $first_name. '"';
  	$gender = '"'. $gender . '"';
		if ($dod_yy == null or $dod_mm == null or $dod_dd == null) {
			$sql = 'insert into Actor (id, last_name, first_name, sex, dob) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name . ',' . $gender . ',' . $dob . ')';
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
			$sql = 'insert into Actor (id, last_name, first_name, sex, dob, dod) values (' . ' ' . $cur_id . ',' . $last_name . ','. $first_name . ',' . $gender . ',' . $dob . ',' .$dod . ')';

		}
    echo $sql."<br>";
    $mysqli->query($sql);
    echo 'Successfully add the information!';
	}
}
?>


<script language="JavaScript"><!--
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


</body>
</html>
