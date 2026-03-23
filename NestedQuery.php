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

    $query = "SELECT CollectionName, Skin_Name, Price AS Cheapest_Price
        FROM Skins NATURAL JOIN SkinInCollection
        NATURAL JOIN Collections
        NATURAL JOIN MarketValue
        WHERE Price = (
            SELECT min(Price)
            FROM MarketValue NATURAL JOIN Skins
            NATURAL JOIN SkinInCollection
            WHERE SkinInCollection.CollectionID = Collections.CollectionID
        )
        ORDER BY Cheapest_Price ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->execute();

    if ($stmt) {
        echo "<div class='row'>";
		echo "<center>";
		echo "<h2>Nested Query</h2>";
		echo "<p>What is the cheapest skin I own in every collection that I own?</p>"; 
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<td>Collection Name</td>";
		echo "<td>Skin Name</td>";
		echo "<td>Cheapest Price</td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$name = $row["Skin_Name"];
            $collection = $row["CollectionName"];
            $price = $row["Cheapest_Price"];
            echo "<tr>";	
			echo "<td>{$collection}</td>";
            echo "<td>{$name}</td>";
			echo "<td>\${$price}</td>";
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