<?php
require "../../database/connexion.php";

// Récupération de l'ID de la commande depuis l'URL
$commandeId = isset($_GET['commande_id']) ? intval($_GET['commande_id']) : 0;

// Récupération des informations de la commande et du client
$userPosition = null;
$userName = '';
$userAddress = '';
$commandeInfo = [];

if ($commandeId > 0) {
    try {
        // Récupérer les informations de la commande et du client
        $sql = "SELECT 
                    c.id as commande_id,
                    c.montant_total,
                    c.statut,
                    c.date_commande,
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.address,
                    u.position
                FROM commandes c
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE c.id = ?";
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$commandeId]);
        $commandeInfo = $stmt->fetch();
        
        if ($commandeInfo && !empty($commandeInfo['position'])) {
            // Extraire les coordonnées de la position
            $position = $commandeInfo['position'];
            
            // Si la position est au format "lat, lng" ou contient des coordonnées
            if (strpos($position, ',') !== false) {
                $coords = explode(',', $position);
                $userPosition = [
                    'lat' => trim($coords[0]),
                    'lng' => trim($coords[1])
                ];
            } else {
                // Tentative de géocodage (si c'est une adresse)
                // Pour l'exemple, on utilise des coordonnées par défaut
                $userPosition = [
                    'lat' => 7.6833,
                    'lng' => -5.0333
                ];
            }
            
            $userName = $commandeInfo['first_name'] . ' ' . $commandeInfo['last_name'];
            $userAddress = $commandeInfo['address'] ?? '';
        }
    } catch (PDOException $e) {
        error_log("Erreur: " . $e->getMessage());
    }
}

// Si aucune position trouvée, utiliser des coordonnées par défaut
if (!$userPosition) {
    $userPosition = [
        'lat' => 7.6833,  // Coordonnées par défaut (Yamoussoukro)
        'lng' => -5.0333
    ];
    $userName = $userName ?: 'Client inconnu';
}

$title = "Position du client - Commande #$commandeId";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | NatureFerme</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/localisation.css">
</head>

<body>
    <div class="container">
        <h2>
            <i class="fas fa-map-marked-alt"></i>
            Localisation du client - Commande #<?= htmlspecialchars($commandeId) ?>
        </h2>

        <?php if (empty($commandeInfo)): ?>
            <div class="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Aucune information trouvée pour cette commande.</p>
            </div>
        <?php endif; ?>

        <div class="header-info">
            <!-- Informations client -->
            <div class="client-info">
                <h3>
                    <i class="fas fa-user-circle"></i>
                    <?= htmlspecialchars($userName) ?>
                </h3>
                <div class="client-details">
                    <?php if (!empty($commandeInfo['email'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <span><?= htmlspecialchars($commandeInfo['email']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($commandeInfo['phone'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-phone"></i>
                            <span><?= htmlspecialchars($commandeInfo['phone']) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($userAddress)): ?>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($userAddress) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($commandeInfo['position'])): ?>
                        <div class="detail-item">
                            <i class="fas fa-location-dot"></i>
                            <span><?= htmlspecialchars($commandeInfo['position']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Badge commande -->
            <?php if (!empty($commandeInfo)): ?>
                <div class="commande-badge">
                    <i class="fas fa-shopping-cart"></i>
                    <div>
                        <div><strong>Montant:</strong> <?= number_format($commandeInfo['montant_total'], 0, ',', ' ') ?> FCFA</div>
                        <div><strong>Statut:</strong> <?= ucfirst($commandeInfo['statut']) ?></div>
                        <div><strong>Date:</strong> <?= date('d/m/Y', strtotime($commandeInfo['date_commande'])) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistiques position -->
        <div class="stats">
            <div class="stat-card">
                <i class="fas fa-map-pin"></i>
                <span>Latitude: <?= $userPosition['lat'] ?>°</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-map-pin"></i>
                <span>Longitude: <?= $userPosition['lng'] ?>°</span>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="btn-group">
            <button class="btn" onclick="centerOnUser()">
                <i class="fas fa-crosshairs"></i>
                Centrer sur le client
            </button>
            <button class="btn btn-secondary" onclick="calculateRoute()">
                <i class="fas fa-route"></i>
                Calculer l'itinéraire
            </button>
            <button class="btn" onclick="window.print()">
                <i class="fas fa-print"></i>
                Imprimer
            </button>
            <button class="btn" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
                Retour
            </button>
        </div>

        <!-- Carte -->
        <div id="map"></div>

        <div class="info-text">
            <i class="fas fa-info-circle"></i>
            Position du client affichée sur la carte - Cliquez sur le marqueur pour plus d'informations
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <script>
        // Coordonnées du client depuis PHP
        var userLat = <?= $userPosition['lat'] ?>;
        var userLng = <?= $userPosition['lng'] ?>;
        var userName = "<?= htmlspecialchars($userName) ?>";
        var userAddress = "<?= htmlspecialchars($userAddress) ?>";
        var userPosition = "<?= htmlspecialchars($commandeInfo['position'] ?? '') ?>";

        // Création de la carte
        var map = L.map('map').setView([userLat, userLng], 15);

        // Fond de carte stylisé
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, © <a href="https://carto.com/">CartoDB</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Icône personnalisée pour le client
        var userIcon = L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-user-circle" style="font-size: 50px; color: #6c3cff; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));"></i>',
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        });

        // Icône pour le livreur (position par défaut)
        var deliveryIcon = L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-truck" style="font-size: 40px; color: #4CAF50; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));"></i>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        // Ajout du marqueur client
        var userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
        
        // Popup avec informations client
        var popupContent = `
            <div style="text-align: center; min-width: 200px;">
                <strong style="color: #6c3cff; font-size: 16px;">👤 Client: ${userName}</strong><br>
                ${userAddress ? `<span style="color: #666;"><i class="fas fa-map-marker-alt"></i> ${userAddress}</span><br>` : ''}
                <span style="color: #666;"><i class="fas fa-location-dot"></i> ${userLat}°N, ${userLng}°E</span><br>
                ${userPosition ? `<small style="color: #999;">${userPosition}</small>` : ''}
            </div>
        `;
        
        userMarker.bindPopup(popupContent).openPopup();

        // Ajout d'un effet de pulsation
        userMarker._icon.classList.add('user-marker');

        // Position du livreur (par défaut, à quelques mètres du client pour l'exemple)
        var deliveryLat = userLat + 0.01;
        var deliveryLng = userLng + 0.01;

        var deliveryMarker = L.marker([deliveryLat, deliveryLng], { icon: deliveryIcon }).addTo(map)
            .bindPopup(`
                <div style="text-align: center;">
                    <strong style="color: #4CAF50;">🚚 Position du livreur</strong><br>
                    <span style="color: #666;">${deliveryLat}°N, ${deliveryLng}°E</span>
                </div>
            `);

        // Variable pour stocker le contrôle de routage
        var route;

        // Fonction pour centrer sur le client
        function centerOnUser() {
            map.setView([userLat, userLng], 16, {
                animate: true,
                duration: 1
            });
            userMarker.openPopup();
        }

        // Fonction pour calculer l'itinéraire
        function calculateRoute() {
            if (route) {
                map.removeControl(route);
            }

            route = L.Routing.control({
                waypoints: [
                    L.latLng(deliveryLat, deliveryLng), // Position du livreur
                    L.latLng(userLat, userLng) // Position du client
                ],
                lineOptions: {
                    styles: [{
                        color: '#6c3cff',
                        weight: 6,
                        opacity: 0.7,
                        dashArray: '10, 10'
                    }]
                },
                createMarker: function() {
                    return null; // On utilise nos propres marqueurs
                },
                routeWhileDragging: false,
                showAlternatives: true,
                fitSelectedRoutes: true,
                show: true,
                collapsible: true,
                language: 'fr'
            }).addTo(map);

            // Zoom sur l'itinéraire
            setTimeout(function() {
                var bounds = L.latLngBounds([deliveryLat, deliveryLng], [userLat, userLng]);
                map.fitBounds(bounds, { padding: [50, 50], animate: true, duration: 1 });
            }, 500);

            // Animation du bouton
            event.target.style.transform = 'scale(0.95)';
            setTimeout(() => {
                event.target.style.transform = 'scale(1)';
            }, 200);
        }

        // Ajout d'une échelle
        L.control.scale({
            imperial: false,
            metric: true,
            position: 'bottomright'
        }).addTo(map);

        // Ajout d'un bouton de localisation
        L.control.locate({
            position: 'topleft',
            strings: {
                title: "Montrer ma position actuelle"
            },
            locateOptions: {
                enableHighAccuracy: true
            }
        }).addTo(map);

        // Gestionnaire d'erreur pour les marqueurs
        map.on('locationerror', function(e) {
            alert("Impossible d'obtenir votre position: " + e.message);
        });

        // Centrer sur le client au chargement
        setTimeout(function() {
            map.setView([userLat, userLng], 15, {
                animate: true,
                duration: 1
            });
        }, 500);

        // Ajout d'un contrôle de zoom
        L.control.zoom({
            position: 'topleft'
        }).addTo(map);
    </script>
</body>
</html>