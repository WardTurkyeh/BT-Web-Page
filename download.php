<?php 

include 'config.php';


$id = $_GET['id']; // Get id from url bar

if (!$id) {
    header("Location: index.php");
}



?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="style.css">

	<title>MVD Result - BIMTEC</title>
</head>
<body>
	<div class="file__upload">
		<div class="header">
			<p><i class="fa fa-download fa-2x"></i><span><span>BI</span><n>M</n>TEC</span></p>			
		</div>
		<form class="body">
			<div class="download">
                <?php
                $sql = "SELECT * FROM uploaded_files WHERE id='$id'";
                $result = sqlsrv_query($conn, $sql);
                if($row = sqlsrv_fetch_array($result)){
                ?>  
                     <a href="uploads/<?php echo $row['Name']; ?>" download="<?php echo $row['New_Name']; ?>" class="download_link"><?php echo $row['New_Name']; ?></a>
                <?php   
                    
                } 
                  
                ?>
                
            </div>
		</form>
	</div>
</body>
</html>