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

    $query = "SELECT Rarity_Name, count(SkinID) AS Number_Of_Skins
        FROM Rarity 
        LEFT OUTER JOIN Skins ON Rarity.RarityID = Skins.RarityID
        GROUP BY Rarity.RarityID
        ORDER BY Rarity_Name ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->execute();

    if ($stmt) {
        echo "<div class='row'>";
		echo "<center>";
		echo "<h2>Dr. Trotter's Query</h2>";
		echo "<p>What is the total number of skins for each rarity ordered alphabetically by the rarity names?</p>"; 
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<td>Rarity Name</td>";
		echo "<td>Number of Skins</td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rarity = $row["Rarity_Name"];
            $numberSkins = $row["Number_Of_Skins"];
            echo "<tr>";	
			echo "<td>{$rarity}</td>";
            echo "<td>{$numberSkins}</td>";
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