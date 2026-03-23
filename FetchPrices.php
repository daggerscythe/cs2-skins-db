<?php
    require_once("session.php");
    require_once("included_functions.php");
    require_once("database.php");

    $inventory_file = '/home/gzhumash/public_html/CS375/FinalProject/csgo_inventory.json';

    echo "Starting price fetch...<br>";
    flush();

    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );

    $process = proc_open("python3 /home/gzhumash/public_html/CS375/FinalProject/getInv.py 4", $descriptorspec, $pipes);

    if (is_resource($process)) {
        fclose($pipes[0]); // Close stdin
        
        echo "Python script started...<br>";
        flush();
        
        // Set non-blocking reads
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        
        $output = "";
        
        while (true) {
            // Read from stdout
            $read = fread($pipes[1], 1024);
            if ($read !== false && $read !== '') {
                $output .= $read;
                echo nl2br(htmlspecialchars($read));
                flush();
            }
            
            // Read from stderr
            $errors = fread($pipes[2], 1024);
            if ($errors !== false && $errors !== '') {
                echo "<span style='color: red'>" . nl2br(htmlspecialchars($errors)) . "</span>";
                flush();
            }
            
            // Check if process is still running
            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }
            
            // Small delay to prevent CPU spinning
            usleep(100000); // 0.1 second
        }
        
        // Clean up
        fclose($pipes[1]);
        fclose($pipes[2]);
        $return_value = proc_close($process);
        
        echo "<br>Script finished with return code: " . $return_value . "<br>";
        flush();
        
        $sql_file = "/home/gzhumash/public_html/CS375/FinalProject/market_value_inserts.sql";
        
        if ($return_value === 0 && file_exists($sql_file)) {
            echo "SQL file found, updating database...<br>";
            flush();
            
            $inserts = file_get_contents($sql_file);
            echo "<pre>SQL Content: " . htmlspecialchars($inserts) . "</pre>";
            flush();
            
            try {
                $mysqli = Database::DBConnect();
                
                // Parse the SQL file and update existing records
                $lines = explode("\n", $inserts);
                foreach ($lines as $line) {
                    if (strpos($line, 'INSERT INTO MarketValue') !== false) {
                        // Extract values from INSERT statement
                        preg_match('/VALUES\s*\((\d+),\s*([\d.]+),\s*\'(.*?)\'\)/', $line, $matches);
                        if (count($matches) === 4) {
                            $skinID = $matches[1];
                            $price = $matches[2];
                            $date = $matches[3];
                            
                            // Update existing record or insert if doesn't exist
                            $update_stmt = $mysqli->prepare("
                                INSERT INTO MarketValue (SkinID, Price, Date) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE Price = VALUES(Price), Date = VALUES(Date)
                            ");
                            $update_stmt->execute([$skinID, $price, $date]);
                        }
                    }
                }
                echo "<span style='color: green'>Prices updated successfully!</span>";
            } catch (Exception $e) {
                echo "<span style='color: red'>SQL Error: " . $e->getMessage() . "</span>";
            }
        } else {
            echo "<span style='color: red'>Error: SQL file not created or script failed</span><br>";
            echo "File exists: " . (file_exists($sql_file) ? 'Yes' : 'No') . "<br>";
            echo "File size: " . (file_exists($sql_file) ? filesize($sql_file) : 0) . " bytes";
        }
    } else {
        echo "<span style='color: red'>Failed to start Python script</span>";
    }

    echo "<br><br><a href='ListSkins.php'>Back to Skins List</a>";
?>