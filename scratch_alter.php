<?php
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
try {
    $conn->exec("ALTER TABLE employees ADD COLUMN avatar VARCHAR(255) NULL AFTER full_name");
    echo "Added avatar successfully";
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
