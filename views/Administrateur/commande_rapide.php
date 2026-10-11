<?php
// Configuration de la base de données

 $host = 'localhost';
    $dbname = 'u213482981_lapinou_store';
    $username = 'root';
    $password = '';


try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Traitement des requêtes AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        if ($_POST['action'] === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = intval($_POST['id']);
            $status = $_POST['status'];
            
            // Validation du statut
            $valid_statuses = ['en_attente', 'confirmee', 'traitee', 'annulee'];
            if (!in_array($status, $valid_statuses)) {
                echo json_encode(['success' => false, 'message' => 'Statut invalide']);
                exit;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE commande_rapide SET status = ? WHERE id = ?");
                $result = $stmt->execute([$status, $id]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            }
            exit;
        }
        
        if ($_POST['action'] === 'delete_command' && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            
            try {
                $stmt = $pdo->prepare("DELETE FROM commande_rapide WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Commande supprimée avec succès']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            }
            exit;
        }
    }
    
    // Récupérer toutes les commandes (correction des noms de colonnes selon votre table)
    $stmt = $pdo->query("SELECT * FROM commandes_rapides ORDER BY date_commande DESC");
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer les statistiques pour le graphique
    $stats = [
        'total' => count($commandes),
        'en_attente' => count(array_filter($commandes, function($cmd) { return $cmd['status'] == 'en_attente'; })),
        'confirmee' => count(array_filter($commandes, function($cmd) { return $cmd['status'] == 'confirmee'; })),
        'traitee' => count(array_filter($commandes, function($cmd) { return $cmd['status'] == 'traitee'; })),
        'annulee' => count(array_filter($commandes, function($cmd) { return $cmd['status'] == 'annulee'; }))
    ];
    
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - NatureFerme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2D5A27;
            --primary-light: #4A8B3A;
            --primary-dark: #1F3E1A;
            --accent: #FF6B35;
            --accent-light: #FF9D71;
            --background: #F8FAF9;
            --surface: #FFFFFF;
            --text: #2C3E50;
            --text-light: #5D6D7E;
            --text-lighter: #8C98A9;
            --border: #E1E8ED;
            --shadow: 0 4px 20px rgba(45, 90, 39, 0.08);
            --shadow-hover: 0 8px 25px rgba(45, 90, 39, 0.12);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            --gradient-accent: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        .header {
            background: var(--gradient);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
            font-size: 2.2rem;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-left: 4px solid var(--primary);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card:hover::after {
            opacity: 1;
        }

        .stat-card h3 {
            color: var(--text-light);
            margin-bottom: 10px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-card p {
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--primary);
            position: relative;
        }

        .stat-card.en_attente { border-left-color: #FFC107; }
        .stat-card.en_attente p { color: #FFC107; }
        
        .stat-card.confirmee { border-left-color: #4CAF50; }
        .stat-card.confirmee p { color: #4CAF50; }
        
        .stat-card.traitee { border-left-color: #2196F3; }
        .stat-card.traitee p { color: #2196F3; }
        
        .stat-card.annulee { border-left-color: #F44336; }
        .stat-card.annulee p { color: #F44336; }

        .filters {
            padding: 20px;
            background: #f8fbf7;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 500;
            color: var(--text);
            min-width: 80px;
        }

        select, input {
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            background: white;
            transition: var(--transition);
            flex: 1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        select:focus, input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .btn {
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(45, 90, 39, 0.1);
        }

        .btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(45, 90, 39, 0.15);
        }

        .btn-secondary {
            background: var(--text-light);
        }

        .btn-secondary:hover {
            background: var(--text);
        }

        .btn-accent {
            background: var(--accent);
        }

        .btn-accent:hover {
            background: var(--accent-light);
        }

        .table-container {
            overflow-x: auto;
            padding: 0 20px 20px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--primary);
            color: white;
            position: sticky;
            top: 0;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        th:hover {
            background: var(--primary-dark);
        }

        th i {
            margin-left: 5px;
            font-size: 0.8rem;
        }

        tr {
            transition: var(--transition);
        }

        tr:nth-child(even) {
            background: #f9fbf9;
        }

        tr:hover {
            background: #f1f8e9;
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .status {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            transition: var(--transition);
        }

        .status-en_attente {
            background: #fff8e1;
            color: #ff8f00;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
        }

        .status-confirmee {
            background: #e8f5e9;
            color: #2e7d32;
            box-shadow: 0 2px 4px rgba(76, 175, 80, 0.2);
        }

        .status-traitee {
            background: #e3f2fd;
            color: #1565c0;
            box-shadow: 0 2px 4px rgba(33, 150, 243, 0.2);
        }

        .status-annulee {
            background: #ffebee;
            color: #c62828;
            box-shadow: 0 2px 4px rgba(244, 67, 54, 0.2);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .edit-btn {
            background: #bbdefb;
            color: #0d47a1;
        }

        .edit-btn:hover {
            background: #90caf9;
        }

        .delete-btn {
            background: #ffcdd2;
            color: #c62828;
        }

        .delete-btn:hover {
            background: #ef9a9a;
        }

        .view-btn {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .view-btn:hover {
            background: #a5d6a7;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 8px;
        }

        .pagination button {
            padding: 10px 16px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .pagination button:hover {
            background: #f1f8e9;
            transform: translateY(-2px);
        }

        .pagination button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: var(--radius);
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            animation: zoomIn 0.3s ease-out forwards;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--primary);
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group select {
            width: 100%;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            background: var(--primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1100;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.error {
            background: #c62828;
        }

        .notification.success {
            background: #2e7d32;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            th, td {
                padding: 10px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>
            <p>Consultez et gérez toutes les commandes passées sur NatureFerme</p>
        </div>
        
        <div class="dashboard">
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Commandes</h3>
                    <p><?php echo $stats['total']; ?></p>
                </div>
                <div class="stat-card en_attente">
                    <h3>En attente</h3>
                    <p><?php echo $stats['en_attente']; ?></p>
                </div>
                <div class="stat-card confirmee">
                    <h3>Confirmées</h3>
                    <p><?php echo $stats['confirmee']; ?></p>
                </div>
                <div class="stat-card traitee">
                    <h3>Traîtées</h3>
                    <p><?php echo $stats['traitee']; ?></p>
                </div>
                <div class="stat-card annulee">
                    <h3>Annulées</h3>
                    <p><?php echo $stats['annulee']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="filters">
            <div class="filter-group">
                <label for="status-filter">Statut:</label>
                <select id="status-filter">
                    <option value="all">Tous</option>
                    <option value="en_attente">En attente</option>
                    <option value="confirmee">Confirmée</option>
                    <option value="traitee">Traîtée</option>
                    <option value="annulee">Annulée</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date-filter">Date:</label>
                <select id="date-filter">
                    <option value="all">Toutes</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="search">Recherche:</label>
                <input type="text" id="search" placeholder="Nom, téléphone, produit...">
            </div>
            
            <button class="btn" onclick="applyFilters()">
                <i class="fas fa-filter"></i> Appliquer
            </button>
            <button class="btn btn-secondary" onclick="resetFilters()">
                <i class="fas fa-sync"></i> Réinitialiser
            </button>
            <button class="btn btn-accent" onclick="exportData()">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
        
        <div class="table-container">
            <table id="commands-table">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">ID <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(1)">Produit <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(2)">Type <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(3)">Quantité <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(4)">Prix Unitaire <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(5)">Total <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(6)">Téléphone <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(7)">Statut <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(8)">Date <i class="fas fa-sort"></i></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($commandes) > 0): ?>
                        <?php foreach ($commandes as $commande): ?>
                            <tr data-id="<?php echo $commande['id']; ?>">
                                <td>#<?php echo $commande['id']; ?></td>
                                <td><?php echo $commande['lapin_id']; ?></td>
                                <td><?php echo htmlspecialchars($commande['lapin_type']); ?></td>
                                <td><?php echo $commande['quantity']; ?></td>
                                <td><?php echo $commande['price_unit']; ?>FCFA</td>
                                <td><strong><?php echo $commande['total_price']; ?>FCFA</strong></td>
                                <td><?php echo $commande['phone']; ?></td>
                                <td>
                                    <span class="status status-<?php echo $commande['status']; ?>">
                                        <?php 
                                        switch($commande['status']) {
                                            case 'en_attente': echo 'En attente'; break;
                                            case 'confirmee': echo 'Confirmée'; break;
                                            case 'traitee': echo 'Traîtée'; break;
                                            case 'annulee': echo 'Annulée'; break;
                                            default: echo $commande['status'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
                                <td class="actions">
                                    <button class="action-btn view-btn" onclick="viewCommand(<?php echo $commande['id']; ?>)">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>
                                    <button class="action-btn edit-btn" onclick="editCommand(<?php echo $commande['id']; ?>, '<?php echo $commande['status']; ?>')">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="action-btn delete-btn" onclick="deleteCommand(<?php echo $commande['id']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                                <p style="color: var(--text-light);">Aucune commande trouvée.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Suivant <i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <!-- Modal pour modifier le statut -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modifier le statut</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="form-group">
                <label for="edit-status">Nouveau statut:</label>
                <select id="edit-status">
                    <option value="en_attente">En attente</option>
                    <option value="confirmee">Confirmée</option>
                    <option value="traitee">Traîtée</option>
                    <option value="annulee">Annulée</option>
                </select>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                <button class="btn" onclick="saveChanges()" id="save-btn">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-text">Opération effectuée avec succès</span>
    </div>

    <script>
        let currentEditId = null;
        let sortColumn = 0;
        let sortDirection = 'asc';

        function applyFilters() {
            const statusFilter = document.getElementById('status-filter').value;
            const dateFilter = document.getElementById('date-filter').value;
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#commands-table tbody tr');
            
            rows.forEach(row => {
                const statusClass = Array.from(row.querySelector('.status').classList).find(cls => cls.startsWith('status-'));
                const status = statusClass ? statusClass.replace('status-', '') : '';
                const dateText = row.cells[8].textContent;
                const text = row.textContent.toLowerCase();
                
                const statusMatch = statusFilter === 'all' || status === statusFilter;
                const dateMatch = dateFilter === 'all' || checkDateFilter(dateText, dateFilter);
                const searchMatch = text.includes(searchTerm);
                
                row.style.display = statusMatch && dateMatch && searchMatch ? '' : 'none';
            });
            
            showNotification('Filtres appliqués avec succès', 'success');
        }
        
        function checkDateFilter(dateText, filterType) {
            // Implémentation simplifiée pour la démo
            return true;
        }
        
        function resetFilters() {
            document.getElementById('status-filter').value = 'all';
            document.getElementById('date-filter').value = 'all';
            document.getElementById('search').value = '';
            
            const rows = document.querySelectorAll('#commands-table tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
            
            showNotification('Filtres réinitialisés', 'success');
        }
        
        function exportData() {
            // Fonctionnalité d'export simplifiée pour la démo
            showNotification('Export des données en cours...', 'success');
            setTimeout(() => {
                showNotification('Données exportées avec succès', 'success');
            }, 1500);
        }
        
        function viewCommand(id) {
            showNotification('Affichage des détails de la commande #' + id, 'success');
            // Ici, vous pouvez rediriger vers une page de détail ou ouvrir un modal
        }
        
        function editCommand(id, currentStatus) {
            currentEditId = id;
            document.getElementById('edit-status').value = currentStatus;
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            currentEditId = null;
        }
        
        function saveChanges() {
            if (currentEditId) {
                const newStatus = document.getElementById('edit-status').value;
                const saveBtn = document.getElementById('save-btn');
                
                // Afficher le loading
                saveBtn.classList.add('loading');
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
                
                // Envoyer la requête AJAX
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('id', currentEditId);
                formData.append('status', newStatus);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour l'affichage
                        updateCommandRow(currentEditId, newStatus);
                        showNotification(data.message, 'success');
                        closeModal();
                        
                        // Recharger la page pour mettre à jour les statistiques
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur de connexion', 'error');
                })
                .finally(() => {
                    // Restaurer le bouton
                    saveBtn.classList.remove('loading');
                    saveBtn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
                });
            }
        }
        
        function updateCommandRow(id, newStatus) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const statusCell = row.querySelector('.status');
                
                // Supprimer l'ancienne classe de statut
                statusCell.className = statusCell.className.replace(/status-\w+/g, '');
                
                // Ajouter la nouvelle classe et le texte
                statusCell.classList.add('status-' + newStatus);
                
                let statusText = '';
                switch(newStatus) {
                    case 'en_attente': statusText = 'En attente'; break;
                    case 'confirmee': statusText = 'Confirmée'; break;
                    case 'traitee': statusText = 'Traîtée'; break;
                    case 'annulee': statusText = 'Annulée'; break;
                    default: statusText = newStatus;
                }
                
                statusCell.textContent = statusText;
                
                // Mettre à jour le bouton de modification
                const editBtn = row.querySelector('.edit-btn');
                editBtn.onclick = function() { editCommand(id, newStatus); };
                
                // Animation de mise à jour
                row.style.backgroundColor = '#e8f5e8';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 2000);
            }
        }
        
        function deleteCommand(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer la commande #' + id + '?')) {
                const formData = new FormData();
                formData.append('action', 'delete_command');
                formData.append('id', id);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Supprimer la ligne du tableau
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-100%)';
                            setTimeout(() => {
                                row.remove();
                            }, 300);
                        }
                        
                        showNotification(data.message, 'success');
                        
                        // Recharger la page pour mettre à jour les statistiques
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur de connexion', 'error');
                });
            }
        }
        
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notification-text');
            const icon = notification.querySelector('i');
            
            notificationText.textContent = message;
            
            // Changer l'icône selon le type
            if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
            } else {
                icon.className = 'fas fa-check-circle';
            }
            
            notification.className = 'notification show ' + type;
            
            setTimeout(() => {
                notification.className = 'notification';
            }, 4000);
        }
        
        function sortTable(columnIndex) {
            const table = document.getElementById('commands-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const headerCells = table.querySelectorAll('th');
            
            // Reset all header icons
            headerCells.forEach(cell => {
                const icon = cell.querySelector('i');
                if (icon) icon.className = 'fas fa-sort';
            });
            
            // Set the appropriate icon for the sorted column
            if (sortColumn === columnIndex) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = columnIndex;
                sortDirection = 'asc';
            }
            
            const currentIcon = headerCells[columnIndex].querySelector('i');
            if (currentIcon) {
                currentIcon.className = sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
            }
            
            rows.sort((a, b) => {
                let aValue = a.cells[columnIndex].textContent.trim();
                let bValue = b.cells[columnIndex].textContent.trim();
                
                // Special handling for status column
                if (columnIndex === 7) {
                    aValue = a.cells[columnIndex].querySelector('.status').textContent.trim();
                    bValue = b.cells[columnIndex].querySelector('.status').textContent.trim();
                }
                
                // Try to convert to number if possible
                const aNum = isNaN(aValue) || aValue.includes('/') ? aValue : Number(aValue.replace(/[^\d.-]/g, ''));
                const bNum = isNaN(bValue) || bValue.includes('/') ? bValue : Number(bValue.replace(/[^\d.-]/g, ''));
                
                let comparison = 0;
                if (typeof aNum === 'number' && typeof bNum === 'number' && !isNaN(aNum) && !isNaN(bNum)) {
                    comparison = aNum - bNum;
                } else {
                    comparison = aValue.localeCompare(bValue);
                }
                
                return sortDirection === 'desc' ? comparison * -1 : comparison;
            });
            
            // Remove existing rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            
            // Add sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Fermer la modal en cliquant en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Raccourcis clavier
        document.addEventListener('keydown', function(event) {
            // Échap pour fermer la modal
            if (event.key === 'Escape') {
                closeModal();
            }
            
            // Entrée pour sauvegarder quand la modal est ouverte
            if (event.key === 'Enter' && document.getElementById('editModal').style.display === 'flex') {
                event.preventDefault();
                saveChanges();
            }
        });
    </script>
</body>
</html>