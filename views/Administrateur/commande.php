<?php
require "database/connexion.php";

// Traitement des actions POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commande_id'])) {
    $commandeId = intval($_POST['commande_id']);

    if (isset($_POST['valider'])) {
        $sql = "UPDATE commandes SET validation = 'validé', statut = 'en cours' WHERE id = ?";
        $message = "Commande #$commandeId validée avec succès";
    } elseif (isset($_POST['annuler'])) {
        $sql = "UPDATE commandes SET validation = 'refusé', statut = 'annulee' WHERE id = ?";
        $message = "Commande #$commandeId annulée";
    } elseif (isset($_POST['livrer'])) {
        $sql = "UPDATE commandes SET statut = 'terminee' WHERE id = ?";
        $message = "Commande #$commandeId marquée comme livrée";
    }

    if (isset($sql)) {
        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$commandeId]);
        } catch (PDOException $e) {
            $message = "Erreur lors de la mise à jour: " . $e->getMessage();
        }
    }
}

// Récupération du total des commandes en attente
try {
    $stmt = $this->con->prepare("SELECT COUNT(*) AS total FROM commandes WHERE statut = 'en attente'");
    $stmt->execute();
    $row = $stmt->fetch();
    $totalCommandesEnAttente = $row['total'];
    $GLOBALS['totalCommandesEnAttente'] = $totalCommandesEnAttente;
} catch (PDOException $e) {
    $GLOBALS['totalCommandesEnAttente'] = 0;
}

// ✅ RÉCUPÉRATION DES COMMANDES AVEC LES INFORMATIONS DES CLIENTS (INCLuant POSITION)
try {
    // Récupérer toutes les commandes avec les informations des clients
    $sql = "SELECT 
                c.id,
                c.user_id,
                c.montant_total,
                c.statut,
                c.validation,
                c.date_commande,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.address,
                u.position
            FROM commandes c
            LEFT JOIN users u ON c.user_id = u.user_id
            ORDER BY c.date_commande DESC";
    
    $stmt = $this->con->prepare($sql);
    $stmt->execute();
    $commandesData = $stmt->fetchAll();
    
    // Construire le tableau des commandes avec leurs produits
    $commandes = [];
    
    // 🔍 Vérifier quelles tables de détails existent
    $tablesDetailsCommande = ['details_commande', 'commande_details', 'order_details', 'ligne_commande', 'items_commande'];
    $tableDetailsTrouvee = null;
    
    foreach ($tablesDetailsCommande as $tableName) {
        try {
            $testStmt = $this->con->query("SELECT 1 FROM $tableName LIMIT 1");
            $tableDetailsTrouvee = $tableName;
            break;
        } catch (PDOException $e) {
            // Table n'existe pas, continuer
            continue;
        }
    }
    
    foreach ($commandesData as $cmd) {
        $produits = [];
        
        // Essayer de récupérer les produits si la table existe
        if ($tableDetailsTrouvee) {
            try {
                $sqlProduits = "SELECT 
                                    dc.produit_id,
                                    dc.quantite,
                                    dc.prix_unitaire,
                                    dc.sous_total,
                                    p.nom,
                                    p.image_url,
                                    p.prix as prix_courant
                                FROM $tableDetailsTrouvee dc
                                LEFT JOIN produits p ON dc.produit_id = p.produit_id
                                WHERE dc.commande_id = ?";
                
                $stmtProduits = $this->con->prepare($sqlProduits);
                $stmtProduits->execute([$cmd['id']]);
                $produits = $stmtProduits->fetchAll();
            } catch (PDOException $e) {
                // Si erreur, laisser produits vide
                $produits = [];
            }
        }
        
        // Construire la structure de la commande
        $commandes[] = [
            'id' => $cmd['id'],
            'user_id' => $cmd['user_id'],
            'montant_total' => $cmd['montant_total'],
            'statut' => $cmd['statut'],
            'validation' => $cmd['validation'],
            'date_commande' => $cmd['date_commande'],
            'client' => [
                'first_name' => $cmd['first_name'],
                'last_name' => $cmd['last_name'],
                'email' => $cmd['email'],
                'phone' => $cmd['phone'],
                'address' => $cmd['address'],
                'position' => $cmd['position'] ?? '' // Position depuis la table users
            ],
            'produits' => $produits
        ];
    }
    
    // Message d'information si pas de table de détails trouvée
    if (!$tableDetailsTrouvee && !empty($commandes)) {
        $message = "⚠️ Attention : Les détails des produits ne peuvent pas être affichés. Vérifiez le nom de votre table de détails commandes.";
    }
    
} catch (PDOException $e) {
    $commandes = [];
    $message = "Erreur lors de la récupération des commandes: " . $e->getMessage();
}

// Variables pour la pagination
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 1;
$title = "Gestion des Commandes - NatureFerme";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Gestion des Commandes - NatureFerme') ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/styles/commande.css">
  <style>
    /* Styles pour la position */
    .position-section {
      margin-top: 15px;
      padding: 12px;
      background: #f0f7ff;
      border-radius: 8px;
      border-left: 4px solid #2196F3;
    }
    
    .position-section h4 {
      margin: 0 0 10px 0;
      font-size: 15px;
      color: #1976D2;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .position-section h4 i {
      color: #2196F3;
    }
    
    .position-coords {
      background: white;
      padding: 10px 15px;
      border-radius: 6px;
      border: 1px solid #e0e0e0;
      font-family: 'Courier New', monospace;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .coords-text {
      color: #333;
      font-weight: 500;
      word-break: break-all;
    }
    
    .btn-map {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: background 0.3s;
      border: none;
    }
    
    .btn-map:hover {
      background: #45a049;
    }
    
    .address-section {
      margin: 10px 0;
      padding: 10px;
      background: #f5f5f5;
      border-radius: 6px;
      border-left: 4px solid #FF9800;
    }

    /* Styles pour les formulaires inline */
    form.inline-form {
      display: inline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-container">
      <div class="logo-container">
        <img src="../assets/images/image.png" alt="Logo NatureFerme">
      </div>
      <div class="header">
        <div>
          <h1><i class="fas fa-shopping-basket"></i> Gestion des Commandes</h1>
          <p>Visualisation et gestion de toutes les commandes clients</p>
        </div>
        <div class="total-commandes">
          <i class="fas fa-chart-bar"></i>
          <span id="total-commandes-count">Commandes: <?= $totalCommandesEnAttente ?></span> 
        </div>
      </div>
    </div>
    <a href="../views/Administrateur/centrale_commande.php" class="btn-link">
      <i class="fas fa-warehouse"></i> Gestion de l'entrepôt des commandes
    </a>

    <?php if (!empty($message)): ?>
      <div class="alert">
        <i class="fas fa-info-circle"></i>
        <div><?= htmlspecialchars($message) ?></div>
      </div>
    <?php endif; ?>

    <div class="filters-container">
      <div class="filter-group">
        <label for="filter-statut"><i class="fas fa-filter"></i> Statut</label>
        <select id="filter-statut" onchange="filterCommandes()">
          <option value="">Tous les statuts</option>
          <option value="en attente">En attente</option>
          <option value="en cours">En cours</option>
          <option value="terminee">Terminée</option>
          <option value="annulee">Annulée</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label for="filter-date"><i class="far fa-calendar-alt"></i> Période</label>
        <select id="filter-date" onchange="filterCommandes()">
          <option value="">Toutes les dates</option>
          <option value="today">Aujourd'hui</option>
          <option value="week">Cette semaine</option>
          <option value="month">Ce mois</option>
          <option value="last-month">Mois dernier</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label for="search"><i class="fas fa-search"></i> Recherche</label>
        <input type="text" id="search" placeholder="ID, client, produit..." oninput="searchProducts()">
        <div class="search-suggestions" id="search-suggestions"></div>
      </div>
    </div>

    <div class="commandes-container" id="commandes-list">
      <?php if (!empty($commandes)): ?>
        <?php foreach ($commandes as $commande): ?>
          <div class="commande-card" 
               data-statut="<?= htmlspecialchars(strtolower($commande['statut'])) ?>" 
               data-date="<?= htmlspecialchars(date('Y-m-d', strtotime($commande['date_commande']))) ?>"
               data-id="<?= $commande['id'] ?>"
               data-client="<?= htmlspecialchars(strtolower($commande['client']['first_name'].' '.$commande['client']['last_name'])) ?>">
            <div class="commande-header">
              <div class="commande-info">
                <div class="commande-id">
                  <i class="fas fa-hashtag"></i> #<?= htmlspecialchars($commande['id']) ?>
                  <span class="user-id">Client #<?= htmlspecialchars($commande['user_id']) ?></span>
                </div>
                
                <div class="commande-client">
                  <i class="fas fa-user"></i>
                  <?= htmlspecialchars($commande['client']['first_name'].' '.$commande['client']['last_name']) ?>
                  
                  <div class="client-contact">
                    <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($commande['client']['email']) ?></span>
                    <?php if (!empty($commande['client']['phone'])): ?>
                      <span><i class="fas fa-phone"></i> <?= htmlspecialchars($commande['client']['phone']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                
                <div class="commande-date">
                  <i class="far fa-calendar-alt"></i>
                  <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?>
                </div>
              </div>
              
              <div class="commande-montant">
                <?= number_format($commande['montant_total'], 0, ',', ' ') ?> FCFA
              </div>
              
              <span class="commande-statut statut-<?= strtolower(str_replace(' ', '-', $commande['statut'])) ?>">
                <?= ucfirst($commande['statut']) ?>
              </span>
            </div>
            
            <!-- Section Adresse client -->
            <?php if (!empty($commande['client']['address'])): ?>
            <div class="address-section">
              <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-map-marker-alt" style="color: #FF9800; font-size: 16px;"></i>
                <strong>Adresse de livraison :</strong>
                <span><?= htmlspecialchars($commande['client']['address']) ?></span>
              </div>
            </div>
            <?php endif; ?>

            <!-- Section Position (coordonnées GPS) avec bouton vers localisation.php -->
            <?php if (!empty($commande['client']['position'])): ?>
            <div class="position-section">
              <h4>
                <i class="fas fa-map-pin"></i>
                Coordonnées GPS du client
              </h4>
              <div class="position-coords">
                <span class="coords-text">
                  <i class="fas fa-location-dot" style="color: #f44336; margin-right: 8px;"></i>
                  <?= htmlspecialchars($commande['client']['position']) ?>
                </span>
                
                <!-- Formulaire POST vers localisation.php -->
                <form action="../views/Administrateur/localisation.php" method="POST" class="inline-form">
                  <input type="hidden" name="position" value="<?= htmlspecialchars($commande['client']['position']) ?>">
                  <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                  <input type="hidden" name="client_nom" value="<?= htmlspecialchars($commande['client']['first_name'].' '.$commande['client']['last_name']) ?>">
                  <input type="hidden" name="client_email" value="<?= htmlspecialchars($commande['client']['email']) ?>">
                  <input type="hidden" name="client_phone" value="<?= htmlspecialchars($commande['client']['phone'] ?? '') ?>">
                  <input type="hidden" name="client_address" value="<?= htmlspecialchars($commande['client']['address'] ?? '') ?>">
                  <input type="hidden" name="montant_total" value="<?= $commande['montant_total'] ?>">
                  <button type="submit" class="btn-map">
                    <i class="fas fa-map-marked-alt"></i> Voir sur la carte
                  </button>
                </form>
              </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($commande['produits'])): ?>
            <div class="commande-details">
              <?php foreach ($commande['produits'] as $produit): ?>
                <div class="produit-item" data-produit="<?= htmlspecialchars(strtolower($produit['nom'])) ?>">
                  <img src="<?= htmlspecialchars($produit['image_url'] ?? '../assets/images/placeholder-produit.png') ?>" 
                       alt="<?= htmlspecialchars($produit['nom']) ?>" 
                       class="produit-image">
                  
                  <div class="produit-info">
                    <div class="produit-nom"><?= htmlspecialchars($produit['nom']) ?></div>
                    <div class="produit-meta">
                      <span><i class="fas fa-box"></i> Quantité: <?= $produit['quantite'] ?>x</span>
                      <span><i class="fas fa-barcode"></i> Réf: #<?= $produit['produit_id'] ?></span>
                      <span><i class="fas fa-money-bill-wave"></i> PU: <?= number_format($produit['prix_unitaire'], 0, ',', ' ') ?> FCFA</span>
                      <?php if (isset($produit['prix_courant'])): ?>
                        <span><i class="fas fa-chart-line"></i> Prix actuel: <?= number_format($produit['prix_courant'], 0, ',', ' ') ?> FCFA</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class="produit-prix">
                    <div class="sous-total"><?= number_format($produit['sous_total'], 0, ',', ' ') ?> FCFA</div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="commande-actions">
              <form action="" method="POST" style="display: flex; gap: 12px;">
                <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">

                <?php if ($commande['statut'] == 'en attente'): ?>
                  <button type="submit" class="btn btn-primary" name="valider">
                    <i class="fas fa-check-circle"></i> Valider
                  </button>
                  <button type="submit" class="btn btn-danger" name="annuler">
                    <i class="fas fa-times-circle"></i> Annuler
                  </button>
                <?php elseif ($commande['statut'] == 'en cours'): ?>
                  <button type="submit" class="btn btn-primary" name="livrer">
                    <i class="fas fa-truck"></i> Livrée
                  </button>
                <?php endif; ?>

                <button type="button" class="btn btn-outline" 
                        onclick="voirDetails(<?= $commande['id'] ?>, [<?= !empty($commande['produits']) ? implode(',', array_column($commande['produits'], 'produit_id')) : ''; ?>])">
                  <i class="fas fa-eye"></i> Détails
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-box-open"></i>
          <h3>Aucune commande trouvée</h3>
          <p>Il semble qu'aucune commande ne corresponde à vos critères de recherche.</p>
          <button class="btn btn-primary" onclick="resetFilters()">
            <i class="fas fa-sync-alt"></i> Réinitialiser
          </button>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($totalPages) && $totalPages > 1): ?>
      <div class="pagination">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>"><i class="fas fa-chevron-left"></i> Précédent</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>" class="<?= $currentPage == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>">Suivant <i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <script>
    async function searchProducts() {
      const searchTerm = document.getElementById('search').value.trim();
      const suggestionsContainer = document.getElementById('search-suggestions');
      
      if (searchTerm.length < 2) {
        suggestionsContainer.style.display = 'none';
        return;
      }
      
      try {
        const response = await fetch(`../api/search_products.php?q=${encodeURIComponent(searchTerm)}`);
        const products = await response.json();
        
        if (products.length > 0) {
          suggestionsContainer.innerHTML = products.map(product => `
            <div class="search-suggestion" onclick="selectProductSuggestion('${product.nom}')">
              ${product.nom} (${product.reference})
            </div>
          `).join('');
          suggestionsContainer.style.display = 'block';
        } else {
          suggestionsContainer.style.display = 'none';
        }
      } catch (error) {
        console.error('Erreur de recherche:', error);
      }
    }
    
    function selectProductSuggestion(productName) {
      document.getElementById('search').value = productName;
      document.getElementById('search-suggestions').style.display = 'none';
      filterCommandes();
    }
    
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.filter-group')) {
        document.getElementById('search-suggestions').style.display = 'none';
      }
    });

    function filterCommandes() {
      const statut = document.getElementById('filter-statut').value.toLowerCase();
      const dateFilter = document.getElementById('filter-date').value;
      const searchTerm = document.getElementById('search').value.toLowerCase();
      
      const today = new Date();
      const cards = document.querySelectorAll('.commande-card');
      let visibleCount = 0;
      
      cards.forEach(card => {
        const cardStatut = card.dataset.statut;
        const cardDate = card.dataset.date;
        const cardId = card.dataset.id;
        const cardClient = card.dataset.client;
        const cardProduits = card.querySelectorAll('.produit-item');
        
        let produitMatch = false;
        cardProduits.forEach(produit => {
          if (produit.dataset.produit && produit.dataset.produit.includes(searchTerm)) {
            produitMatch = true;
          }
        });
        
        const statutMatch = !statut || cardStatut === statut;
        const searchMatch = !searchTerm || 
                          (cardId && cardId.includes(searchTerm)) || 
                          (cardClient && cardClient.includes(searchTerm)) || 
                          produitMatch;
        
        let dateMatch = true;
        if (dateFilter && cardDate) {
          const cardDateObj = new Date(cardDate);
          
          switch (dateFilter) {
            case 'today':
              dateMatch = cardDate === today.toISOString().split('T')[0];
              break;
            case 'week':
              const startOfWeek = new Date(today);
              startOfWeek.setDate(today.getDate() - today.getDay());
              dateMatch = cardDateObj >= startOfWeek;
              break;
            case 'month':
              dateMatch = cardDateObj.getMonth() === today.getMonth() && 
                         cardDateObj.getFullYear() === today.getFullYear();
              break;
            case 'last-month':
              const lastMonth = new Date(today);
              lastMonth.setMonth(lastMonth.getMonth() - 1);
              dateMatch = cardDateObj.getMonth() === lastMonth.getMonth() && 
                         cardDateObj.getFullYear() === lastMonth.getFullYear();
              break;
          }
        }
        
        if (statutMatch && searchMatch && dateMatch) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });
      
      document.getElementById('total-commandes-count').textContent = 'Commandes: ' + visibleCount;
    }
    
    function resetFilters() {
      document.getElementById('filter-statut').value = '';
      document.getElementById('filter-date').value = '';
      document.getElementById('search').value = '';
      document.getElementById('search-suggestions').style.display = 'none';
      filterCommandes();
    }
    
    function voirDetails(commandeId, produitIds) {
      if (produitIds && produitIds.length > 0) {
        window.location.href = `../views/Administrateur/commande_details.php?commande_id=${commandeId}&produits=${produitIds.join(',')}`;
      } else {
        window.location.href = `../views/Administrateur/commande_details.php?commande_id=${commandeId}`;
      }
    }
    
    function initFiltersFromUrl() {
      const urlParams = new URLSearchParams(window.location.search);
      
      if (urlParams.has('statut')) {
        document.getElementById('filter-statut').value = urlParams.get('statut');
      }
      
      if (urlParams.has('search')) {
        document.getElementById('search').value = urlParams.get('search');
      }
      
      if (urlParams.has('date')) {
        document.getElementById('filter-date').value = urlParams.get('date');
      }
      
      filterCommandes();
    }
    
    document.addEventListener('DOMContentLoaded', initFiltersFromUrl);
  </script>
</body>
</html>