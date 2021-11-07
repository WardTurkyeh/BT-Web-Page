<?php
if (isset($_POST["result"])){
	header("location:https://www.google.jo/");
}
include 'config.php';

$link = "";
$link_status = "display: none;";

/**************************************************************************************************************************************** */

//Importing data to SQL server

if (isset($_POST['upload'])){           //If isset upload button or not
	
	
	
	//Declaring Variables
	
	$ext = "mdb";
	$ext2 = "accdb";
	$location = "uploads/";
	$file_new_name = date("dmy") . time() . $_FILES["file"]["name"]; //New unique names for the uploaded MVD
	$file_name = $_FILES["file"]["name"];  //Get uploaded file name
	$file_temp = $_FILES["file"]["tmp_name"]; //Get uploaded file temp
	$file_size = $_FILES["file"]["size"]; //Get uploaded file size
	$path ='C:\xampp\htdocs\MVD Upload - BIMTEC\uploads'; 

	

	if (strpos($file_name, $ext ) || strpos($file_name, $ext2 ) !== false){

		$sql = "DROP TABLE IF EXISTS uploaded_files
					CREATE TABLE uploaded_files ("
					. " id VARCHAR(250) NOT NULL,"  
					. " Name VARCHAR(250) NOT NULL,"           // Creating tables in SQL					 
					. " New_Name VARCHAR(250) NOT NULL"
					. ")";
		$result = sqlsrv_query($conn, $sql);
	
		
		$sql2 = "INSERT INTO uploaded_files (id, Name, New_Name) 
				VALUES ('1','$file_name', '$file_new_name')";                                 //Adding the file's data into SQL server 
		$result2 = sqlsrv_query($conn, $sql2);



		move_uploaded_file($file_temp, $location . $file_name);          //moving the file to its new location
		echo  "<font color=cyan>$file_name</font>" . "<font color=white> was successfully uploaded!</font> " ;
	
		
		$db = $path . DIRECTORY_SEPARATOR . $file_name;
		$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)}; Dbq=".$db."; Uid=; Pwd=;");   //connecting to the file

		$acc1 = "SELECT * FROM TOC";             			//Selecting the main table that has the names of the other tables
		$resultAcc= $db->query($acc1);

		$tableName=[];

		foreach($resultAcc->fetchAll() as $col=>$tName){  		//Getting the name of tables from the uploaded file

			array_push($tableName, $tName[1]);
		}
		
		$noOftable = count($tableName);    			//counting the number of tables needed
		//print_r($tableName[1]);


		for($t=0; $t < $noOftable; $t++){					//looping on the number of tables

			$acc2 = "SELECT * FROM [$tableName[$t]]";             //Selecting tables from the uploaded file to get theri columns
			$resultAcc2= $db->query($acc2);

			$acc3 = "SELECT * FROM [$tableName[$t]]";             //Selecting the same tables from the uploaded file to get their data
			$resultAcc3= $db->query($acc2);

			$sql2 = "DROP TABLE IF EXISTS [$tableName[$t]]
					CREATE TABLE [$tableName[$t]] ("
					. " col VARCHAR(50) NOT NULL"           // Creating tables in SQL
					. ")";
			$result2 = sqlsrv_query($conn, $sql2);
			
			
			$row_array = [];
			$nameOfcol = [];


			foreach($resultAcc2->fetch(PDO::FETCH_ASSOC) as $key=>$row){        //Getting the table columns names

				array_push($row_array, $row);
				array_push($nameOfcol, $key);									
																			//Addin the column names to SQL
				//print_r($key);
				$sql3 = "ALTER TABLE [$tableName[$t]]
						ADD $key varchar(255)
						ALTER TABLE [$tableName[$t]]
						DROP COLUMN col";
				
				$result3 = sqlsrv_query($conn, $sql3);
			}

			$noOfcol = count($nameOfcol); 								 // getting the number of columns
			$num=$noOfcol-1;


			for ($n=0; $n<$noOfcol; $n++){             						  //looping through the number of columns	

				foreach ($resultAcc3->fetchAll() as $col=>$column){			//looping through the number of elements in each column 
					

					//print_r($column);
					$sql4="INSERT INTO [$tableName[$t]] VALUES (";            //adding the elements to the SQL statement 'Generating SQL statement'

					for ($n=0; $n<$num; $n++){									//adding the elements to the SQL table using generated SQL statement

						$sql4= $sql4 . "'"."$column[$n]"."', ";
					}	
					$sql4 = $sql4. "'"."$column[$num]"."'".")";
					$result4 = sqlsrv_query($conn, $sql4);
				}
			}
		}

		// Select id from database
		$sql = "SELECT id FROM uploaded_files";
		$result = sqlsrv_query($conn, $sql);
		if ($row = sqlsrv_fetch_array($result)) {
			$link = $base_url . "download.php?id=" . $row['id'];
			$link_status = "display: block;";
		}
	
	} else{
		echo "<script>alert('Error! Incorrect file extension. Only mdb & accdb files')</script>";			// an error if the file extension was wrong!
	}	

}


/******************************************************************************************************************************************************************/

?>


<!------------------------------------------------------------------------------------------>
<!DOCTYPE html>            
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<title>MVD Upload - BIMTEC</title>
</head>
<body>
	<div class="file__upload">
		<div class="header">
			<p><i class="fa fa-cloud-upload fa-2x"></i><span><span>BI</span><n>M</n>TEC</span></p>
		</div>
		<form action="" method="POST" enctype="multipart/form-data" class="body">
			<!-- Sharable Link Code -->
			<!--<input type="checkbox" id="link_checkbox">
			<input type="text" value="<?php echo $link; ?>" id="link" readonly>
			<label for="link_checkbox" style="<?php echo $link_status; ?>">Get Results</label>-->
			<button name="result">Get Results!!</button> 
		
			<input type="file" name="file" id="upload" required>
			<label for="upload">
				<i class="fa fa-file-text-o fa-3x"></i>
				<p>
					<b>Browse</b> <strong>MVD files </strong> here<br>
					 to upload and check
				</p>
			</label>
			<button name="upload" class="btn">Upload</button>
		</form>
	</div>
</body>
</html>