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
            font-size: 16px;
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
        <th>FamilyName</th>
        <th>TypeName</th>
        <th>IfcExportAs</th>
        <th>PredefinedType</th>
        <th>SystemClassification</th>
        <th>SystemName</th>
        <th>SystemID</th>
        <th>Result</th>
    </tr>
<?php 

include 'config.php';

$sql1 = "DROP TABLE IF EXISTS [RevitColdWaterDraw_offPoint_Splitted]
            CREATE TABLE RevitColdWaterDraw_offPoint_Splitted ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "FamilyName varchar(300) NOT NULL,"
            . "TypeName varchar(300) NOT NULL,"
            . "IfcExportAs varchar(300) NOT NULL,"
            . "PredefinedType varchar(300) NOT NULL,"
            . "SystemClassification varchar(300) NOT NULL,"
            . "SystemName varchar(300) NOT NULL,"
            . "SystemID varchar(300) NOT NULL"
            .")";
$result1 = sqlsrv_query($conn, $sql1); 


$sql2 = "INSERT INTO RevitColdWaterDraw_offPoint_Splitted
        SELECT Category, ObjectID, FamilyName, TypeName, IfcExportAs, PredefinedType, SystemClassification, SystemName, (Value) FROM [dbo].[Revitcoldwaterdrawoffpoint]
        CROSS APPLY string_split(SystemID, ',')";
$result2 = sqlsrv_query($conn, $sql2); 

$sql3 = "DROP TABLE IF EXISTS [RevitColdWaterStorageTanks_Splitted]
            CREATE TABLE RevitColdWaterStorageTanks_Splitted ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "FamilyName varchar(300) NOT NULL,"
            . "TypeName varchar(300) NOT NULL,"
            . "IfcExportAs varchar(300) NOT NULL,"
            . "PredefinedType varchar(300) NOT NULL,"
            . "SystemClassification varchar(300) NOT NULL,"
            . "SystemName varchar(300) NOT NULL,"
            . "SystemID varchar(300) NOT NULL"
            .")";
$result3 = sqlsrv_query($conn, $sql3); 


$sql4 = "INSERT INTO RevitColdWaterStorageTanks_Splitted
        SELECT Category, ObjectID, FamilyName, TypeName, IfcExportAs, PredefinedType, SystemClassification, SystemName, (Value) FROM [dbo].[Revitcoldwaterstoragetank]
        CROSS APPLY string_split(SystemID, ',')";
$result4 = sqlsrv_query($conn, $sql4); 


$sql5 = "DROP TABLE IF EXISTS [Unmatched]
            CREATE TABLE Unmatched ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "FamilyName varchar(300) NOT NULL,"
            . "TypeName varchar(300) NOT NULL,"
            . "IfcExportAs varchar(300) NOT NULL,"
            . "PredefinedType varchar(300) NOT NULL,"
            . "SystemClassification varchar(300) NOT NULL,"
            . "SystemName varchar(300) NOT NULL,"
            . "SystemID varchar(300) NOT NULL"
            .")";
$result5 = sqlsrv_query($conn, $sql5); 

$sql6 = "INSERT INTO Unmatched
        SELECT RevitColdWaterDraw_offPoint_Splitted.Category, RevitColdWaterDraw_offPoint_Splitted.ObjectID, RevitColdWaterDraw_offPoint_Splitted.FamilyName, RevitColdWaterDraw_offPoint_Splitted.TypeName, RevitColdWaterDraw_offPoint_Splitted.IfcExportAs, RevitColdWaterDraw_offPoint_Splitted.PredefinedType, RevitColdWaterDraw_offPoint_Splitted.SystemClassification, RevitColdWaterDraw_offPoint_Splitted.SystemName, RevitColdWaterDraw_offPoint_Splitted.SystemID
        FROM RevitColdWaterDraw_offPoint_Splitted LEFT JOIN RevitColdWaterStorageTanks_Splitted ON RevitColdWaterDraw_offPoint_Splitted.[SystemID] = RevitColdWaterStorageTanks_Splitted.[SystemID]
        WHERE (((RevitColdWaterStorageTanks_Splitted.SystemID) Is Null))";
$result6 = sqlsrv_query($conn, $sql6); 
        
$sql7 = "DROP TABLE IF EXISTS [Matched]     
            CREATE TABLE Matched ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "FamilyName varchar(300) NOT NULL,"
            . "TypeName varchar(300) NOT NULL,"
            . "IfcExportAs varchar(300) NOT NULL,"
            . "PredefinedType varchar(300) NOT NULL,"
            . "SystemClassification varchar(300) NOT NULL,"
            . "SystemName varchar(300) NOT NULL,"
            . "SystemID varchar(300) NOT NULL"
            .")";
$result7 = sqlsrv_query($conn, $sql7);

$sql8 = "INSERT INTO Matched
        SELECT RevitColdWaterDraw_offPoint_Splitted.Category, RevitColdWaterDraw_offPoint_Splitted.ObjectID, RevitColdWaterDraw_offPoint_Splitted.FamilyName, RevitColdWaterDraw_offPoint_Splitted.TypeName, RevitColdWaterDraw_offPoint_Splitted.IfcExportAs, RevitColdWaterDraw_offPoint_Splitted.PredefinedType, RevitColdWaterDraw_offPoint_Splitted.SystemClassification, RevitColdWaterDraw_offPoint_Splitted.SystemName, RevitColdWaterDraw_offPoint_Splitted.SystemID
        FROM RevitColdWaterDraw_offPoint_Splitted LEFT JOIN Unmatched ON RevitColdWaterDraw_offPoint_Splitted.[SystemID] = Unmatched.[SystemID]
        WHERE (((Unmatched.SystemID) Is Null))";

$result8 = sqlsrv_query($conn, $sql8);

$sql9 = "ALTER TABLE Matched ADD RESULT VARCHAR(300)
        ALTER TABLE Unmatched ADD RESULT VARCHAR(300)";
$result9 = sqlsrv_query($conn, $sql9);      

$sql10 = "DROP TABLE IF EXISTS [FinalResult_1902020202] 
            CREATE TABLE FinalResult_1902020202 ("
            . "Category varchar(300) NOT NULL,"
            . "ObjectID varchar(300) NOT NULL,"
            . "FamilyName varchar(300) NOT NULL,"
            . "TypeName varchar(300) NOT NULL,"
            . "IfcExportAs varchar(300) NOT NULL,"
            . "PredefinedType varchar(300) NOT NULL,"
            . "SystemClassification varchar(300) NOT NULL,"
            . "SystemName varchar(300) NOT NULL,"
            . "SystemID varchar(300) NOT NULL,"
            . "Result varchar(300) NOT NULL"
            .")";
$result10 = sqlsrv_query($conn, $sql10);   

$sql11 = "INSERT INTO FinalResult_1902020202
        SELECT Matched.Category, Matched.ObjectID, Matched.FamilyName, Matched.TypeName, Matched.IfcExportAs, Matched.PredefinedType, Matched.SystemClassification, Matched.SystemName, Matched.SystemID, ISNULL(RESULT,'PASS') AS RESULT FROM Matched
        INSERT INTO FinalResult_1902020202
        SELECT Unmatched.Category, Unmatched.ObjectID, Unmatched.FamilyName, Unmatched.TypeName, Unmatched.IfcExportAs, Unmatched.PredefinedType, Unmatched.SystemClassification, Unmatched.SystemName, Unmatched.SystemID, ISNULL(RESULT,'FAIL') AS RESULT FROM Unmatched";
$result11 = sqlsrv_query($conn, $sql11);

$sql12 = "SELECT * FROM FinalResult_1902020202";

$result12 = sqlsrv_query($conn, $sql12);

while ($values = sqlsrv_fetch_array( $result12)){
			
	echo"<tr><td>".$values["Category"]."</td><td>". $values["ObjectID"]."</td><td>". $values["FamilyName"]."</td><td>". $values["TypeName"]."</td><td>".$values["IfcExportAs"]."</td><td>".$values["PredefinedType"]."</td><td>".$values["SystemClassification"]."</td><td>".$values["SystemName"]."</td><td>".$values["SystemID"]."</td><td>". $values["Result"]."</td></tr>";
}
	echo"</table>";


?>

</table>
</body>
</html>


    
