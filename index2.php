<!DOCTYPE html>
<html>
<head>
    <title> RESULTS </title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            color: #2c9ebd;
            font-family: monospace;
            font-size: 25px;
            text-align: left;
            background-color: white;
             }
        th {
            background-color: #2c9ebd;
            color: white;
        }
    </style>
</head>
<body>

<table>
    <tr>
        <th>Name</th>
        <th>Area</th>
        <th>Volume</th>
        <th>Field5</th>
        <th>WallID</th>
    </tr>
<?php 

include 'config.php';
$sql6 = "DROP TABLE IF EXISTS [Room_Schedule_Splitted]
			CREATE TABLE Room_Schedule_Splitted ("
			. " Name varchar(300) NOT NULL,"
			. "Area varchar(300) NOT NULL,"
			. "Volume varchar(300) NOT NULL,"
			. "Field5 varchar(300) NOT NULL,"
			. "WallID varchar(300) NOT NULL"
			.")";
$result6 = sqlsrv_query($conn, $sql6);
$sql7 = "INSERT INTO Room_Schedule_Splitted
		SELECT Name, Area, Volume, Field5, Value FROM [dbo].[Room Schedule]
		CROSS APPLY string_split(Wall, ',')";

$result7 = sqlsrv_query($conn, $sql7);

$sql8 = "SELECT * FROM [dbo].[Room_Schedule_Splitted]";
$result8 = sqlsrv_query($conn, $sql8);
		
while ($values = sqlsrv_fetch_array( $result8)){
			
	echo"<tr><td>".$values["Name"]."</td><td>". $values["Area"]."</td><td>". $values["Volume"]."</td><td>". $values["Field5"]."</td><td>". $values["WallID"]."</td></tr>";
}
	echo"</table>";
?>

</table>
</body>
</html>


    
