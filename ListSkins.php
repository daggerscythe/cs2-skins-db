<?php 
	require_once("session.php"); 
	require_once("included_functions.php");
	require_once("database.php");

	ini_set("display_errors", 1); 
	error_reporting(E_ALL);

	new_header("Dani's CS2 Skins Collection"); 
	$mysqli = Database::DBConnect();
	$mysqli -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	function getSortArrow($currentSort, $column, $currentDirection) {
		if ($currentSort === $column) {
			return $currentDirection === 'asc' ? ' ↑' : ' ↓';
		}
		return '';
	}

	if (($output = message()) !== null) {
		echo $output;
	}

	$sort = isset($_GET['sort']) ? $_GET['sort'] : 'skin_name';
	$direction = isset($_GET['dir']) ? $_GET['dir'] : 'asc';
	$allowed_sorts = ['skin_name', 'item_name', 'wear_name', 'rarity_name', 'pattern', 'stattrak', 'case_name', 'collection_name', 'price'];

	if (isset($_GET['sort'])) {
		if ($_GET['sort'] === $sort) {
			$direction = ($_GET['dir'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
		} else {
			$direction = 'asc';
		}
	}


	$sort_column = in_array($sort, $allowed_sorts) ? $sort : 'skin_name';

	$sort_map = [
		'skin_name' => 'Skin_Name',
		'item_name' => 'Item_Name', 
		'wear_name' => 'Wear_Name',
		'rarity_name' => 'Rarity_Name',
		'pattern' => 'Pattern',
		'stattrak' => 'StatTrak',
		'case_name' => 'CaseName',
		'collection_name' => 'CollectionName',
		'price' => 'Price'
	];

	$order_by = $sort_map[$sort_column] . " " . strtoupper($direction);

	$query = "SELECT SkinID, Skin_Name, Item_Name, Wear_Name, Rarity_Name, Pattern, StatTrak, CaseName, CollectionName, Price 
		FROM Skins 
		NATURAL JOIN Items 
		NATURAL JOIN Floats 
		NATURAL JOIN Rarity 
		NATURAL JOIN SkinInCollection 
		NATURAL JOIN Collections 
		NATURAL JOIN Cases 
		NATURAL JOIN MarketValue
		ORDER BY $order_by";

	$stmt = $mysqli->prepare($query);
	$stmt -> execute(); 

    if ($stmt) {
		$count_stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM Skins");
		$count_stmt->execute();
		$count_row = $count_stmt->fetch(PDO::FETCH_ASSOC);
		$total_skins = $count_row['total'];

        echo "<div class='row'>";
		echo "<center>";
		echo "<h2>Skins</h2>";
		echo "<p>Total skins in collection: <strong>{$total_skins}</strong></p>"; 
		echo "<table>";
		echo "<thead>";
		echo "<tr><td></td>";
		echo "<td><a href='ListSkins.php?sort=skin_name&dir=".($sort == 'skin_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Name".getSortArrow($sort, 'skin_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=item_name&dir=".($sort == 'item_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Item Type".getSortArrow($sort, 'item_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=wear_name&dir=".($sort == 'wear_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Float".getSortArrow($sort, 'wear_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=rarity_name&dir=".($sort == 'rarity_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Rarity".getSortArrow($sort, 'rarity_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=pattern&dir=".($sort == 'pattern' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Pattern".getSortArrow($sort, 'pattern', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=stattrak&dir=".($sort == 'stattrak' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>StatTrak".getSortArrow($sort, 'stattrak', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=case_name&dir=".($sort == 'case_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Found in".getSortArrow($sort, 'case_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=collection_name&dir=".($sort == 'collection_name' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Collection".getSortArrow($sort, 'collection_name', $direction)."</a></td>";
		echo "<td><a href='ListSkins.php?sort=price&dir=".($sort == 'price' ? (($_GET['dir'] ?? 'asc') == 'asc' ? 'desc' : 'asc') : 'asc')."'>Price".getSortArrow($sort, 'price', $direction)."</a></td>";
		echo "<td></td></tr>";
		echo "</thead>";
		echo "<tbody>";

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$name = $row["Skin_Name"];
			$item = $row["Item_Name"];
			$float = $row["Wear_Name"];
			$rarity = $row["Rarity_Name"];
			$pattern = $row['Pattern'];
			$statTrak = $row['StatTrak'] == 1 ? "Yes" : "No";
			$case = $row["CaseName"];
            $collection = $row["CollectionName"];
            $price = $row["Price"];
            echo "<tr>";	
			echo "<td><a href='DeleteSkin.php?id=".
			urlencode($row['SkinID'])."' onclick='return confirm(\"Are you sure?\");'>
			<img src='red_x_icon.jpg' width='25px' height='25px'></a></td>";
			echo "<td>{$name}</td>";
			echo "<td>{$item}</td>";
			echo "<td>{$float}</td>";
			echo "<td>{$rarity}</td>";
			echo "<td>{$pattern}</td>";
			echo "<td>{$statTrak}</td>";
			echo "<td>{$case}</td>";
			echo "<td>{$collection}</td>";
			echo "<td>\${$price}</td>";
			echo "<td><a href='UpdateSkin.php?id=".
			urlencode($row['SkinID'])."'>Edit</a></td>";
        }
        echo "</tbody>";
		echo "</table>";
		echo "<p><a href=CreateSkin.php>Add a Skin</a></p>";
		echo "<p><a href=NestedQuery.php>Nested Query: What is the cheapest skin I own in every collection that I own?</a></p>";
		echo "<p><a href=AggregateQuery.php>Aggregate Query: Show all the skins and their collections from 'Souvenir' Cases</a></p>";
		echo "<p><a href=TrotterQuery.php>Dr. Trotter's query: What is the total number of skins for each rarity ordered alphabetically by the rarity names?</a></p>";
		echo "</center>";
		echo "</div>";
    }
    
	new_footer(); 
    Database::DBDisconnect();
 ?>