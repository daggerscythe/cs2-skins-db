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
    echo "<center></center>";
	echo "<h3>Add a Skin</h3>";

	if (isset($_POST["submit"])) {

		if( (isset($_POST["name"]) && $_POST["name"] !== "") 
            && (isset($_POST["item"]) && $_POST["item"] !== "") 
            && (isset($_POST["float"]) && $_POST["float"] !== "")
            && (isset($_POST["rarity"]) && $_POST["rarity"] !== "") 
            && (isset($_POST["case"]) && $_POST["case"] !== "")
            && (isset($_POST["pattern"]) && $_POST["pattern"] !== "")
            && (isset($_POST["statTrak"]) && $_POST["statTrak"] !== "")
            && (isset($_POST["price"]) && $_POST["price"] !== "")) {

            // GENERATE SKIN ID
            $item_type_to_num = array(
                'RIFL' => 1, 'SNPR' => 2, 'PIST' => 3, 'SMG' => 4, 
                'SHTG' => 5, 'MCHG' => 6, 'KNIF' => 7, 'GLOV' => 7, 
                'AGNT' => 7, 'OTHR' => 8
            );
            $float_to_num = array('FN'=>1,'MW'=>2,'FT'=>3,'WW'=>4,'BS'=>5);
            $rarity_to_num = array('CG'=>1,'IG'=>2,'MS'=>3,'RS'=>4,'CL'=>5,'CV'=>6,'EX'=>7);
            
            $p1 = $item_type_to_num[$_POST['item']] ?? 8;
            $p2 = $float_to_num[$_POST['float']] ?? 1;
            $p3 = $rarity_to_num[$_POST['rarity']] ?? 1;

            // Extract digits from skin name
            preg_match_all('/\d/', $_POST['name'], $matches);
            $digits = implode('', $matches[0]);
            if (strlen($digits) >= 4) {
                $p4 = substr($digits, 0, 4);
            } else {
                $p4 = str_pad($digits, 4, '0');
            }

            $base = $p1 . $p2 . $p3 . $p4;
            $base_int = intval($base);

            $SkinID = $base_int;
            $counter = 1;

            // Check if SkinID already exists in database
            $checkQuery = "SELECT SkinID FROM Skins WHERE SkinID = ?";
            $checkStmt = $mysqli->prepare($checkQuery);

            while (true) {
                $checkStmt->execute([$SkinID]);
                if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                    break; // SkinID doesn't exist
                }
                // Collision, try again
                $SkinID = intval($base_int . $counter);
                $counter++;
            }


            $query = "INSERT INTO Skins VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($query);
            
            $stmt -> execute([$SkinID, $_POST['name'], $_POST['item'], $_POST['pattern'], 
                $_POST['float'], $_POST['rarity'], $_POST['statTrak']]);

			if ($stmt){
                // Get the collection ID for the selected case
                $query = "SELECT CollectionID FROM Collections WHERE CaseID = ?";
                $stmt_collection = $mysqli->prepare($query);
                $stmt_collection->execute([$_POST['case']]);
                $collectionRow = $stmt_collection->fetch(PDO::FETCH_ASSOC);

                if (!$collectionRow) {
                    $_SESSION["message"] = "Error! No collection found for selected case.";
                    redirect("CreateSkin.php");
                }

                $query2 = "INSERT INTO SkinInCollection VALUES(?, ?)";
                $stmt2 = $mysqli->prepare($query2);
                $stmt2 -> execute([$collectionRow['CollectionID'], $SkinID]);

                if ($stmt2) {
                    $_SESSION["message"] = $_POST["name"]." has been added.";
                } else {
                    $_SESSION["message"] = "Error! Could not add skin ".$_POST["name"];
                }

                $query2 = "INSERT INTO MarketValue (SkinID, Price, Date) VALUES(?, ?, CURDATE())";
                $stmt2 = $mysqli->prepare($query2);
                $stmt2 -> execute([$SkinID, $_POST['price']]);

                if ($stmt2) {
                    $_SESSION["message"] = $_POST["name"]." has been added.";
                } else {
                    $_SESSION["message"] = "Error! Could noot add skin ".$_POST["name"];
                }
            }
            
			redirect("ListSkins.php");
		}
		else {
			
            $_SESSION["message"] = "Unable to add skin. Fill in all information!";

			redirect("CreateSkin.php");
		}
	}
	else {

            echo "<form method='POST' action='CreateSkin.php'>";
            // Name
            echo "<p>Name: <input type=text name='name'></p>";
            // Item Type
            echo "<p>Item Type: <select name='item'>";
            echo "<option></option>";
            $stmt3 = $mysqli->prepare("SELECT * FROM Items");
            $stmt3 -> execute();  
            while($row = $stmt3->fetch(PDO::FETCH_ASSOC)){
				echo "<option value='".$row['ItemID']."'>".$row['Item_Name']."</option>";
			}  
            echo "</select><p />";
            // Float
            echo "<p>Float: <select name='float'>";
            echo "<option></option>";
            $stmt4 = $mysqli->prepare("SELECT * FROM Floats");
            $stmt4 -> execute();  
            while($row = $stmt4->fetch(PDO::FETCH_ASSOC)){
				echo "<option value='".$row['FloatID']."'>".$row['Wear_Name']."</option>";
			}  
            echo "</select><p />";
            // Rarity
            echo "<p>Rarity: <select name='rarity'>";
            echo "<option></option>";
            $stmt5 = $mysqli->prepare("SELECT * FROM Rarity");
            $stmt5 -> execute();  
            while($row = $stmt5->fetch(PDO::FETCH_ASSOC)){
				echo "<option value='".$row['RarityID']."'>".$row['Rarity_Name']."</option>";
			}  
            echo "</select><p />";
            // Case
            echo "<p>Case: <select name='case'>";
            echo "<option></option>";
            $stmt6 = $mysqli->prepare("SELECT * FROM Cases");
            $stmt6 -> execute();  
            while($row = $stmt6->fetch(PDO::FETCH_ASSOC)){
				echo "<option value='".$row['CaseID']."'>".$row['CaseName']."</option>";
			}  
            echo "</select><p />";
            // Pattern
            echo "<p>Pattern: <input type=number name='pattern'></p>";
            // StatTrak
            echo "<p>Is it StatTrak? 
            <input type='radio' name='statTrak' value='1'> Yes
            <input type='radio' name='statTrak' value='0' checked> No
            </p>";
            echo "<p>Price: $ <input type='number' name='price' step='0.01' min='0'></p>";
            echo "<input type='submit' name='submit' value='Add a Skin' class='tiny round button'>";
            echo "</form>";
         		
	}

	echo "<br /><br /><a href='ListSkins.php'>Back to Main Page</a>";
	echo "</label>";
	echo "</div>";

    new_footer(); 
    Database::DBDisconnect();
?>