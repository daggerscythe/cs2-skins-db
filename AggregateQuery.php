<?php 
	require_once("session.php"); 
	require_once("included_functions.php");
	require_once("database.php");

	ini_set("display_errors", 1); 
	error_reporting(E_ALL);

	new_header("Dani's CS2 Skins Collection"); 
	$mysqli = Database::DBConnect();
	$mysqli -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (($output = message()) !== null) {
		echo $output;
	}

    $query = "SELECT Skin_Name, CollectionName
        FROM Skins INNER JOIN SkinInCollection ON Skins.SkinID = SkinInCollection.SkinID
        INNER JOIN Collections ON SkinInCollection.CollectionID = Collections.CollectionID
        WHERE Collections.CaseID IN (
            SELECT CaseID 
            FROM Cases 
            WHERE CaseName LIKE '%Souvenir%'
        )
        ORDER BY CollectionName ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->execute();

    if ($stmt) {
        echo "<div class='row'>";
		echo "<center>";
		echo "<h2>Aggregate Query</h2>";
		echo "<p>Show all the skins and their collections from 'Souvenir' Cases</p>"; 
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<td>Skin Name</td>";
		echo "<td>Collection Name</td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$name = $row['Skin_Name'];
            $collection = $row["CollectionName"];
            echo "<tr>";	
			echo "<td>{$name}</td>";
            echo "<td>{$collection}</td>";
            echo "</tr>";
        }
        echo "</tbody>";
		echo "</table>";
        echo "<p><a href=ListSkins.php>Back to Main Page</a></p>";
		echo "</center>";
		echo "</div>";
    }

    new_footer(); 
    Database::DBDisconnect();

?>