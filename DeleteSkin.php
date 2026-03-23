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
	
  	if (isset($_GET["id"]) && $_GET["id"] !== "") {
		
		$query = "DELETE FROM MarketValue WHERE SkinID = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->execute([$_GET["id"]]);

		$query = "DELETE FROM SkinInCollection WHERE SkinID = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->execute([$_GET["id"]]);

		$query = "DELETE FROM Skins WHERE SkinID = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->execute([$_GET["id"]]);
  
		if ($stmt) {
			$_SESSION["message"] = "Skin was successfully deleted.";
		}
		else {
			$_SESSION["message"] = "Skin could not be deleted.";
		}
			
	}
	else {

		$_SESSION["message"] = "Skin could not be found!";

	}

	redirect("ListSkins.php");
	new_footer(); 
    Database::DBDisconnect();
?>