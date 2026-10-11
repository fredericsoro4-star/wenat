<?php
// Connexion a la base : fonctionne avec Docker ET en local (XAMPP/WAMP)
$host = getenv('DB_HOST') ?: 'localhost';
$name = getenv('DB_NAME') ?: 'u213482981_lapinou_store';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion a la base de donnees.');
}
