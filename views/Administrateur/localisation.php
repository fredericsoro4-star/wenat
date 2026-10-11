<?php
// localisation.php
session_start();
require "../../database/connexion2.php";

// Récupération des données POST
$position = $_POST['position'] ?? '';
$commande_id = $_POST['commande_id'] ?? '';
$client_nom = $_POST['client_nom'] ?? '';
$client_email = $_POST['client_email'] ?? '';
$client_phone = $_POST['client_phone'] ?? '';
$client_address = $_POST['client_address'] ?? '';
$montant_total = $_POST['montant_total'] ?? '';

// Extraction des coordonnées du client
$coordinates = explode(',', str_replace(' ', '', $position));
$client_lat = $coordinates[0] ?? '';
$client_lng = $coordinates[1] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localisation - Commande #<?= htmlspecialchars($commande_id) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }

        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i { color: #4CAF50; }

        .client-info {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .client-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
        }

        .client-info-item i { color: #4CAF50; width: 20px; }

        .btn-retour {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-retour:hover { background: #5a6268; }

        .map-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        #map {
            height: 600px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .info-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 18px;
            border-left: 5px solid #4CAF50;
        }

        .info-message i {
            font-size: 30px;
            color: #4CAF50;
        }

        /* Bandeau de statut GPS en temps réel */
        #gps-status {
            background: #fff3e0;
            color: #e65100;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            border-left: 5px solid #FF9800;
            transition: all 0.3s ease;
        }

        #gps-status.active {
            background: #e3f2fd;
            color: #0d47a1;
            border-color: #2196F3;
        }

        #gps-status i { font-size: 20px; }

        /* Panneau distance */
        #distance-panel {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            flex-wrap: wrap;
        }

        .distance-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 120px;
        }

        .distance-item .d-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .distance-item .d-value {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .distance-item .d-value.near { color: #4CAF50; }
        .distance-item .d-value.far  { color: #F44336; }

        .location-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .detail-item i {
            width: 30px;
            height: 30px;
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-item .label { color: #666; font-size: 12px; }
        .detail-item .value { font-weight: bold; color: #333; }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            text-decoration: none;
        }

        .btn-action:hover { background: #1976D2; }

        /* Indicateur GPS pulsé */
        @keyframes pulse {
            0%   { transform: scale(1);   opacity: 1; }
            50%  { transform: scale(1.4); opacity: 0.5; }
            100% { transform: scale(1);   opacity: 1; }
        }
        .pulse-dot {
            width: 12px; height: 12px;
            background: #2196F3;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>
                <i class="fas fa-map-marked-alt"></i>
                Localisation - Commande #<?= htmlspecialchars($commande_id) ?>
            </h1>

            <div class="client-info">
                <div class="client-info-item">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($client_nom) ?></span>
                </div>
                <div class="client-info-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span><?= number_format($montant_total, 0, ',', ' ') ?> FCFA</span>
                </div>
            </div>

            <a href="commande.php" class="btn-retour">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <?php if (empty($client_lat) || empty($client_lng)): ?>
    <div class="error-message">
        <i class="fas fa-exclamation-triangle"></i>
        <div>Position du client non disponible ou invalide</div>
    </div>
    <?php else: ?>

    <div class="map-container">

        <!-- Statut GPS en temps réel -->
        <div id="gps-status">
            <i class="fas fa-satellite-dish"></i>
            <span id="gps-status-text">Recherche de votre position GPS...</span>
            <span class="pulse-dot" id="pulse" style="display:none;"></span>
        </div>

        <!-- Panneau de distance -->
        <div id="distance-panel" style="display:none;">
            <div class="distance-item">
                <span class="d-label"><i class="fas fa-ruler"></i> Distance</span>
                <span class="d-value" id="dist-value">—</span>
            </div>
            <div class="distance-item">
                <span class="d-label"><i class="fas fa-map-pin"></i> Votre position</span>
                <span class="d-value" style="font-size:13px; color:#555;" id="my-coords">—</span>
            </div>
            <div class="distance-item">
                <span class="d-label"><i class="fas fa-user"></i> Client</span>
                <span class="d-value" style="font-size:13px; color:#555;"><?= $client_lat ?>, <?= $client_lng ?></span>
            </div>
        </div>

        <div id="map"></div>

        <div class="location-details">
            <div class="detail-item">
                <i class="fas fa-map-pin"></i>
                <div>
                    <div class="label">Position du client</div>
                    <div class="value"><?= htmlspecialchars($position) ?></div>
                </div>
            </div>

            <?php if (!empty($client_address)): ?>
            <div class="detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <div class="label">Adresse complète</div>
                    <div class="value"><?= htmlspecialchars($client_address) ?></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="detail-item">
                <i class="fas fa-phone"></i>
                <div>
                    <div class="label">Téléphone</div>
                    <div class="value"><?= htmlspecialchars($client_phone ?: 'Non renseigné') ?></div>
                </div>
            </div>

            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <div class="label">Email</div>
                    <div class="value"><?= htmlspecialchars($client_email ?: 'Non renseigné') ?></div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($position) ?>"
               target="_blank"
               class="btn-action">
                <i class="fas fa-external-link-alt"></i>
                Ouvrir dans Google Maps
            </a>

            <button onclick="copierPosition()" class="btn-action" style="background: #9C27B0; margin-left: 10px;">
                <i class="fas fa-copy"></i>
                Copier les coordonnées client
            </button>
        </div>
    </div>

    <script>
        // ─── Position fixe du CLIENT (venant du serveur) ─────────────────────────
        const clientLat = <?= (float)$client_lat ?>;
        const clientLng = <?= (float)$client_lng ?>;
        const clientPos = [clientLat, clientLng];

        // ─── Variables globales ───────────────────────────────────────────────────
        let map, myMarker, clientMarker, routeLine;
        let watchId = null;

        // ─── Initialisation de la carte ──────────────────────────────────────────
        function initMap() {
            map = L.map('map').setView(clientPos, 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Marqueur CLIENT (vert, fixe)
            const clientIcon = L.divIcon({
                html: `<div style="background:#4CAF50;width:22px;height:22px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4);"></div>`,
                iconSize: [22, 22],
                iconAnchor: [11, 11],
                popupAnchor: [0, -14]
            });

            clientMarker = L.marker(clientPos, { icon: clientIcon })
                .addTo(map)
                .bindPopup(`<b>👤 Client : <?= htmlspecialchars($client_nom) ?></b><br>Commande #<?= $commande_id ?>`)
                .openPopup();

            // Cercle autour du client
            L.circle(clientPos, {
                color: '#4CAF50',
                fillColor: '#4CAF50',
                fillOpacity: 0.1,
                radius: 80
            }).addTo(map).bindTooltip('Zone de livraison');

            // Démarrer le suivi GPS
            startGPSTracking();
        }

        // ─── Suivi GPS en temps réel ──────────────────────────────────────────────
        function startGPSTracking() {
            if (!navigator.geolocation) {
                updateStatus('error', 'GPS non supporté par ce navigateur.');
                return;
            }

            updateStatus('searching', 'Recherche de votre position GPS...');

            watchId = navigator.geolocation.watchPosition(
                onPositionSuccess,
                onPositionError,
                {
                    enableHighAccuracy: true,   // ← GPS précis
                    maximumAge: 5000,            // Position en cache max 5 sec
                    timeout: 15000              // Timeout 15 sec
                }
            );
        }

        // ─── Callback succès GPS ──────────────────────────────────────────────────
        function onPositionSuccess(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const accuracy = pos.coords.accuracy; // en mètres
            const myPos = [lat, lng];

            // Mise à jour du statut
            updateStatus('active', `GPS actif — Précision : ±${Math.round(accuracy)} m`);

            // Afficher le panneau de distance
            document.getElementById('distance-panel').style.display = 'flex';
            document.getElementById('my-coords').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

            // Calculer et afficher la distance
            const dist = haversineDistance(lat, lng, clientLat, clientLng);
            const distEl = document.getElementById('dist-value');
            if (dist < 1000) {
                distEl.textContent = `${Math.round(dist)} m`;
                distEl.className = 'd-value ' + (dist < 200 ? 'near' : '');
            } else {
                distEl.textContent = `${(dist / 1000).toFixed(2)} km`;
                distEl.className = 'd-value far';
            }

            // ─── Marqueur MA POSITION (bleu, mobile) ────────────────────────────
            const myIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:30px;height:30px;">
                        <div style="background:#2196F3;width:20px;height:20px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4);position:absolute;top:5px;left:5px;"></div>
                        <div style="background:rgba(33,150,243,0.25);width:30px;height:30px;border-radius:50%;position:absolute;top:0;left:0;"></div>
                    </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15],
                popupAnchor: [0, -18]
            });

            if (!myMarker) {
                // Créer le marqueur la première fois
                myMarker = L.marker(myPos, { icon: myIcon })
                    .addTo(map)
                    .bindPopup(`<b>📍 Votre position</b><br>Précision : ±${Math.round(accuracy)} m`);

                // Centrer la carte pour voir les deux points
                fitBothMarkers(myPos);
            } else {
                // Déplacer le marqueur existant
                myMarker.setLatLng(myPos);
                myMarker.getPopup().setContent(`<b>📍 Votre position</b><br>Précision : ±${Math.round(accuracy)} m`);
            }

            // ─── Ligne reliant les deux positions ───────────────────────────────
            if (routeLine) map.removeLayer(routeLine);
            routeLine = L.polyline([myPos, clientPos], {
                color: '#FF5722',
                weight: 3,
                dashArray: '8, 6',
                opacity: 0.8
            }).addTo(map).bindTooltip(`Distance : ${dist < 1000 ? Math.round(dist)+' m' : (dist/1000).toFixed(2)+' km'}`);
        }

        // ─── Callback erreur GPS ──────────────────────────────────────────────────
        function onPositionError(err) {
            let msg = '';
            switch(err.code) {
                case err.PERMISSION_DENIED:
                    msg = "Accès GPS refusé. Autorisez la localisation dans votre navigateur.";
                    break;
                case err.POSITION_UNAVAILABLE:
                    msg = "Position GPS indisponible. Vérifiez votre signal.";
                    break;
                case err.TIMEOUT:
                    msg = "Délai GPS dépassé. Nouvelle tentative...";
                    break;
                default:
                    msg = "Erreur GPS inconnue.";
            }
            updateStatus('error', msg);
        }

        // ─── Mise à jour du bandeau de statut ────────────────────────────────────
        function updateStatus(type, text) {
            const el = document.getElementById('gps-status');
            const textEl = document.getElementById('gps-status-text');
            const pulse = document.getElementById('pulse');
            textEl.textContent = text;

            el.className = '';
            if (type === 'active') {
                el.style.background = '#e3f2fd';
                el.style.color = '#0d47a1';
                el.style.borderColor = '#2196F3';
                pulse.style.display = 'inline-block';
            } else if (type === 'error') {
                el.style.background = '#ffebee';
                el.style.color = '#c62828';
                el.style.borderColor = '#F44336';
                pulse.style.display = 'none';
            } else {
                el.style.background = '#fff3e0';
                el.style.color = '#e65100';
                el.style.borderColor = '#FF9800';
                pulse.style.display = 'inline-block';
            }
        }

        // ─── Ajuster la vue pour voir les deux marqueurs ──────────────────────────
        function fitBothMarkers(myPos) {
            const bounds = L.latLngBounds([myPos, clientPos]);
            map.fitBounds(bounds, { padding: [60, 60] });
        }

        // ─── Formule de Haversine (distance en mètres) ────────────────────────────
        function haversineDistance(lat1, lng1, lat2, lng2) {
            const R = 6371000; // rayon Terre en mètres
            const toRad = x => x * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat/2) * Math.sin(dLat/2)
                    + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2))
                    * Math.sin(dLng/2) * Math.sin(dLng/2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // ─── Copier les coordonnées du client ─────────────────────────────────────
        function copierPosition() {
            const position = "<?= htmlspecialchars($position) ?>";
            navigator.clipboard.writeText(position).then(
                () => alert('Position client copiée !'),
                () => alert('Erreur lors de la copie')
            );
        }

        // ─── Lancement ───────────────────────────────────────────────────────────
        window.onload = initMap;

        // Libérer le watchPosition quand on quitte la page
        window.onbeforeunload = () => {
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
        };
    </script>
    <?php endif; ?>
</body>
</html>