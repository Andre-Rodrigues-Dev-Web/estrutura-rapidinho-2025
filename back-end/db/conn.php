<?php 
    $host = "localhost";
    $db_name = "rapidinho_2025";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);