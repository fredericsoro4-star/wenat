<?php
// api/search_rabbits.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/connexion.php';

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    
    $stmt = $pdo->prepare("
        SELECT 
            rabbit_id,
            name,
            breed,
            age_months,
            slug
        FROM rabbits 
        WHERE name LIKE ? OR breed LIKE ? OR slug LIKE ?
        ORDER BY name 
        LIMIT 10
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatter les résultats pour l'affichage
    $formattedResults = array_map(function($rabbit) {
        return [
            'id' => $rabbit['rabbit_id'],
            'nom' => $rabbit['name'],
            'race' => $rabbit['breed'],
            'age' => $rabbit['age_months'],
            'slug' => $rabbit['slug'],
            'display' => $rabbit['name'] . ' (' . $rabbit['breed'] . ', ' . $rabbit['age_months'] . ' mois)'
        ];
    }, $results);
    
    echo json_encode($formattedResults);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données']);
}
?>