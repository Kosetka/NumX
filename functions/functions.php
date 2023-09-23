<?php

    $BLOCK_TIME = 14;
    

    function setLastAccessDateToJanuary1st2023($db, $phoneNumber = null) {
        try {
            $newLastAccessDate = '2023-01-01 00:00:00';

            $updateQuery = "UPDATE numbers SET last_access_date = :newLastAccessDate";

            if ($phoneNumber !== "null") {
                $updateQuery .= " WHERE phone_number = :phoneNumber";
            }

            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':newLastAccessDate', $newLastAccessDate, PDO::PARAM_STR);
            
            if ($phoneNumber !== "null") {
                $updateStmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
            }

            $updateStmt->execute();
            return true;

        } catch (PDOException $e) {
            return false;
        }
    }

    function getQuantityFromDatabase($db, $city, $databaseType, $action) {
        global $BLOCK_TIME;
        try {
            //              ACTION 
            //      1 => wszystkie numery
            //      2 => zablokowane
            //      3 => dostępne
            //      4 => tymczasowo zablokowane
            try {
                switch ($action) {
                    case 1:
                        // Wszystkie numery
                        $query = "SELECT COUNT(*) AS quantity FROM numbers WHERE city = :city AND database_type = :databaseType";
                        break;
                    case 2:
                        $query = "SELECT COUNT(*) AS quantity FROM numbers WHERE city = :city AND database_type = :databaseType AND is_blocked = 1";
                        break;
                    case 3:
                        $query = "SELECT COUNT(*) AS quantity FROM numbers WHERE city = :city AND database_type = :databaseType AND is_blocked = 0 AND last_access_date <= DATE_SUB(NOW(), INTERVAL $BLOCK_TIME DAY)";
                        break;
                    case 4:
                        $query = "SELECT COUNT(*) AS quantity FROM numbers WHERE city = :city AND database_type = :databaseType AND is_blocked = 0 AND last_access_date >= DATE_SUB(NOW(), INTERVAL $BLOCK_TIME DAY)";
                        break;
                    default:
                        // Domyślna akcja - wszystkie numery
                        $query = "SELECT COUNT(*) AS quantity FROM numbers WHERE city = :city AND database_type = :databaseType";
                        break;
                }
        
                $stmt = $db->prepare($query);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':databaseType', $databaseType, PDO::PARAM_STR);
                $stmt->execute();
        
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $quantity = isset($result['quantity']) ? $result['quantity'] : 0;
        
                return $quantity;
        
            } catch (PDOException $e) {
                return false; 
            }

        } catch (PDOException $e) {
            return false; 
        }
    }


?>