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

	echo "<div class='row'>";
	echo "<label for='left-label' class='left inline'>";
	echo "<h3>Update a Skin</h3>";

	if (isset($_POST["submit"])) {

        if( (isset($_POST["name"]) && $_POST["name"] !== "") 
            && (isset($_POST["item"]) && $_POST["item"] !== "") 
            && (isset($_POST["float"]) && $_POST["float"] !== "")
            && (isset($_POST["rarity"]) && $_POST["rarity"] !== "") 
            && (isset($_POST["case"]) && $_POST["case"] !== "")
            && (isset($_POST["pattern"]) && $_POST["pattern"] !== "")
            && (isset($_POST["statTrak"]) && $_POST["statTrak"] !== "")
            && (isset($_POST["price"]) && $_POST["price"] !== "")) {

            $query = "UPDATE Skins SET Skin_Name = ?, ItemID = ?, Pattern = ?, FloatID = ?, RarityID = ?, StatTrak = ? WHERE SkinID = ?";     
            $stmt = $mysqli->prepare($query);
            $stmt->execute([$_POST['name'], $_POST['item'], $_POST['pattern'], $_POST['float'], $_POST['rarity'], $_POST['statTrak'], $_POST['SkinID']]);

            if($stmt) {
                $price_query = "UPDATE MarketValue SET Price = ?, Date = CURDATE() WHERE SkinID = ?";
                $price_stmt = $mysqli->prepare($price_query);
                $price_stmt->execute([$_POST['price'], $_POST['SkinID']]);
                if ($price_stmt) {
                    $_SESSION["message"] = $_POST["name"]." has been changed";
                } else {
                    $_SESSION["message"] = "Error! Could not add to MarketValue ".$_POST["name"];
                }
                
            }
            else {
                $_SESSION["message"] = "Error! Could not change ".$_POST["name"];
            }

            redirect("ListSkins.php");
        
        }
		else {
            $_SESSION["message"] = "Skin could not be changed, fill in all the information.";
            redirect("UpdateSkin.php?id=".urlencode($_POST["SkinID"]));
        }
	}
	else {

		if (isset($_GET["id"]) && $_GET["id"] !== "") {
			
			$query = "SELECT SkinID, Skin_Name, ItemID, Item_Name, FloatID, Wear_Name, RarityID, Rarity_Name, Pattern, StatTrak, CaseID, CaseName, CollectionName, Price 
                FROM Skins 
                NATURAL JOIN Items 
                NATURAL JOIN Floats 
                NATURAL JOIN Rarity 
                NATURAL JOIN SkinInCollection 
                NATURAL JOIN Collections 
                NATURAL JOIN Cases 
                NATURAL JOIN MarketValue
                WHERE SkinID = ?";
	  		$stmt = $mysqli->prepare($query);
            $stmt->execute([$_GET["id"]]);

            if ($stmt)  {

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                echo "<h5>".$row["Skin_Name"]." Information</h5>";

                echo "<form method='POST' action='UpdateSkin.php'>";
                echo "<input type='hidden' name='SkinID' value='".$row['SkinID']."'/>";
                // Name
                echo "<p>Name: <input type=text name='name' value='".$row['Skin_Name']."'></p>";
                // Item Type
                echo "<p>Item Type: <select name='item'>";
                $stmt2 = $mysqli->prepare("SELECT * FROM Items");
                $stmt2->execute();  
                while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($row2['ItemID'] == $row['ItemID']) ? "selected" : "";
                    echo "<option value='".$row2['ItemID']."' $selected>".$row2['Item_Name']."</option>";
                }  
                echo "</select><p />";
                // Float
                echo "<p>Float: <select name='float'>";
                $stmt3 = $mysqli->prepare("SELECT * FROM Floats");
                $stmt3->execute();  
                while($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($row3['FloatID'] == $row['FloatID']) ? "selected" : "";
                    echo "<option value='".$row3['FloatID']."' $selected>".$row3['Wear_Name']."</option>";
                }  
                echo "</select><p />";
                // Rarity
                echo "<p>Rarity: <select name='rarity'>";
                $stmt4 = $mysqli->prepare("SELECT * FROM Rarity");
                $stmt4->execute();  
                while($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($row4['RarityID'] == $row['RarityID']) ? "selected" : "";
                    echo "<option value='".$row4['RarityID']."' $selected>".$row4['Rarity_Name']."</option>";
                }  
                echo "</select><p />";
                // Case
                echo "<p>Case: <select name='case'>";
                $stmt5 = $mysqli->prepare("SELECT * FROM Cases");
                $stmt5->execute();  
                while($row5 = $stmt5->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($row5['CaseID'] == $row['CaseID']) ? "selected" : "";
                    echo "<option value='".$row5['CaseID']."' $selected>".$row5['CaseName']."</option>";
                }  
                echo "</select><p />";
                // Pattern
                echo "<p>Pattern: <input type=number name='pattern' value='".$row['Pattern']."'></p>";
                // StatTrak
                echo "<p>Is it StatTrak? 
                <input type='radio' name='statTrak' value='1' ".($row['StatTrak'] == 1 ? "checked" : "")."> Yes
                <input type='radio' name='statTrak' value='0' ".($row['StatTrak'] == 0 ? "checked" : "")."> No
                </p>";
                echo "<p>Price: $ <input type='number' name='price' step='0.01' min='0' value='".$row['Price']."'></p>";
                echo "<input type='submit' name='submit' value='Update a Skin' class='tiny round button'>";
                echo "</form>";
               
                echo "<br /><p><a href='ListSkins.php'>Back to Main Page</a>";
                echo "</label>";
                echo "</div>";
            }
            else {
                $_SESSION["message"] = "Skin could not be found!";
                redirect("ListSkins.php");
            }
	  }
    }
					
    new_footer(); 
    Database::DBDisconnect();
?>