<!DOCTYPE html>
<html>
<head>
    <title> RESULTS </title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            color: #2c9ebd;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            text-align: left;
            background-color: white;
             }
        th {
            background-color: #2c9ebd;
            color: white;
        }
        tr:nth-child(even) {background-color: #f2f2f2}
    </style>
</head>
<body>

<table>
    <tr>
        <th>Category</th>
        <th>ObjectID</th>
        <th>RoomArea_m²</th>
        <th>RoomVolume_m³</th>
        <th>RoomHeight_m</th>
        <th>Result</th>
    </tr>
<?php 

include 'config.php';

$sql1 = "DROP TABLE IF EXISTS [RevitColdStoreArea]
            CREATE TABLE RevitColdStoreArea ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomArea DECIMAL NOT NULL,"
            . "AreaUnit varchar(300) NOT NULL"
            .")";
$result1 = sqlsrv_query($conn, $sql1); 

$sql2 = "DROP TABLE IF EXISTS [RevitColdStoreVolume]
            CREATE TABLE RevitColdStoreVolume ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomVolume DECIMAL NOT NULL,"
            . "VolumeUnit varchar(300) NOT NULL"
            .")";
$result2 = sqlsrv_query($conn, $sql2); 


$sql3 = "WITH UNIT_CTE AS
        (SELECT Category, ObjectID, RoomArea, RoomVolume, VALUE, ROW_NUMBER() OVER(partition by ObjectID Order by ObjectID) as RowNum
            From [RevitColdStoreRoom] CROSS APPLY string_split(RoomArea, ' '))
        INSERT INTO RevitColdStoreArea(Category, ObjectID,RoomArea,AreaUnit)
        SELECT Category, ObjectID, [1] AS RoomArea, [2] AS AreaUnit FROM UNIT_CTE
        PIVOT (MAX(VALUE)"."FOR RowNum in ([1],[2])) as PVT";
$result3 = sqlsrv_query($conn, $sql3); 



$sql4 = "WITH UNIT_CTE AS
        (SELECT Category, ObjectID, RoomArea, RoomVolume, VALUE
        , ROW_NUMBER() OVER(partition by ObjectID Order by ObjectID) as RowNum
        From [dbo].[RevitColdStoreRoom]
        CROSS APPLY
        string_split(RoomVolume, ' ')
        )

        INSERT INTO RevitColdStoreVolume(Category, ObjectID,RoomVolume,VolumeUnit)

        SELECT Category, ObjectID,
        [1] AS RoomVolume, [2] AS VolumeUnit
        FROM UNIT_CTE
        PIVOT
        (MAX(VALUE)
        FOR RowNum in ([1],[2])) as PVT";
$result4 = sqlsrv_query($conn, $sql4); 



$sql5 = "ALTER TABLE RevitColdStoreArea ADD RESULT VARCHAR(300)
        ALTER TABLE RevitColdStoreVolume ADD RESULT VARCHAR(300)";
$result5 = sqlsrv_query($conn, $sql5);   

$sql6 = "DROP TABLE IF EXISTS [RevitColdStore]
            CREATE TABLE RevitColdStore ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomArea DECIMAL NOT NULL,"
            . "AreaUnit varchar(300) NOT NULL,"
            . "RoomVolume DECIMAL NOT NULL,"
            . "VolumeUnit varchar(300) NOT NULL"
            .")";
$result6 = sqlsrv_query($conn, $sql6); 


$sql7 = "INSERT INTO RevitColdStore(Category, ObjectID,RoomArea,AreaUnit,RoomVolume,VolumeUnit)
            SELECT A.Category, A.ObjectID, A.RoomArea, A.AreaUnit, 
            V.RoomVolume,V.VolumeUnit
            FROM RevitColdStoreArea AS A
            JOIN RevitColdStoreVolume AS V
            ON A.ObjectID=V.ObjectID";
$result7 = sqlsrv_query($conn, $sql7); 

$sql8 = "ALTER TABLE RevitColdStore ADD RESULT VARCHAR(300)";
$result8 = sqlsrv_query($conn, $sql8);

$sql9 = "DROP TABLE IF EXISTS [Pass]
            CREATE TABLE Pass ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomArea DECIMAL NOT NULL,"
            . "AreaUnit varchar(300) NOT NULL,"
            . "RoomVolume DECIMAL NOT NULL,"
            . "VolumeUnit varchar(300) NOT NULL,"
            . "RoomHeight DECIMAL(3,1),"
            . "Result varchar(300) NOT NULL"
            .")";
$result9 = sqlsrv_query($conn, $sql9); 

$sql10 = "INSERT INTO Pass
            SELECT Category, ObjectID, RoomArea, AreaUnit, RoomVolume, VolumeUnit, (RoomVolume/RoomArea) AS RoomHeight, ISNULL(Result,'PASS') AS Result
            FROM RevitColdStore
            WHERE (RoomVolume/RoomArea) >= 2.5";
$result10 = sqlsrv_query($conn, $sql10); 


$sql11 = "DROP TABLE IF EXISTS [Fail]
            CREATE TABLE Fail ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomArea DECIMAL NOT NULL,"
            . "AreaUnit varchar(300) NOT NULL,"
            . "RoomVolume DECIMAL NOT NULL,"
            . "VolumeUnit varchar(300) NOT NULL,"
            . "RoomHeight DECIMAL(3,1),"
            . "Result varchar(300) NOT NULL"
            .")";
$result11 = sqlsrv_query($conn, $sql11); 


$sql12 = "INSERT INTO Fail
            SELECT Category, ObjectID, RoomArea, AreaUnit, RoomVolume, VolumeUnit, (RoomVolume/RoomArea) AS RoomHeight, ISNULL(Result,'FAIL') AS Result
            FROM RevitColdStore
            WHERE (RoomVolume/RoomArea) <= 2.5";
$result12 = sqlsrv_query($conn, $sql12); 



$sql14 = "DROP TABLE IF EXISTS [FinalResult_1503020201]
            CREATE TABLE FinalResult_1503020201 ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "RoomArea DECIMAL NOT NULL,"
            . "AreaUnit varchar(300) NOT NULL,"
            . "RoomVolume DECIMAL NOT NULL,"
            . "VolumeUnit varchar(300) NOT NULL,"
            . "RoomHeight DECIMAL(3,1),"
            . "Result varchar(300) NOT NULL"
            .")";
$result14 = sqlsrv_query($conn, $sql14); 

$sql15 = " INSERT INTO FinalResult_1503020201
            SELECT * FROM Pass";
$result15 = sqlsrv_query($conn, $sql15);

$sql16 = " INSERT INTO FinalResult_1503020201
            SELECT * FROM Fail";
$result16 = sqlsrv_query($conn, $sql16);


$sql17 = "SELECT Category, ObjectID, RoomArea, RoomVolume, RoomHeight, Result  FROM FinalResult_1503020201";

$result17 = sqlsrv_query($conn, $sql17);


while ($values = sqlsrv_fetch_array( $result17)){
			
	echo"<tr><td>".$values["Category"]."</td><td>". $values["ObjectID"]."</td><td>". $values["RoomArea"]."</td><td>".$values["RoomVolume"]."</td><td>".$values["RoomHeight"]."</td><td>".$values["Result"]."</td></tr>";
}
	echo"</table>";


?>

</table>
</body>
</html>


    
