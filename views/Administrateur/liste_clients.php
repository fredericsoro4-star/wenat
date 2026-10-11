<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des Clients - Lapinou Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #00C853;
      --primary-light: #5EFC82;
      --primary-dark: #007E33;
      --secondary: #8B9467;
      --accent: #4E342E;
      --text: #2E3D49;
      --light: #F9F5F0;
      --white: #FFFFFF;
      --cream: #FFF9F0;
      --gold: #D4AF37;
      --shadow: 0 12px 40px rgba(0, 200, 83, 0.15);
      --shadow-hover: 0 20px 60px rgba(0, 200, 83, 0.25);
      --radius: 24px;
      --radius-sm: 12px;
      --transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
      --transition-fast: all 0.3s ease-out;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background-color: var(--light);
      color: var(--text);
      line-height: 1.7;
      overflow-x: hidden;
      padding: 20px;
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(0, 200, 83, 0.03) 0%, transparent 30%),
        radial-gradient(circle at 90% 80%, rgba(139, 148, 103, 0.03) 0%, transparent 30%);
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(245,255,245,0.95));
      z-index: -2;
    }

    body::after {
      content: '';
      position: fixed;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background-image: 
        url('data:image/svg+xml;utf8,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M30,50 Q50,30 70,50 T90,50" stroke="%2300C853" stroke-width="0.3" fill="none" opacity="0.1"/></svg>'),
        url('data:image/svg+xml;utf8,<svg width="80" height="80" viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg"><circle cx="40" cy="40" r="3" fill="%238B9467" opacity="0.1"/></svg>');
      background-size: 100px 100px, 80px 80px;
      animation: grain 8s steps(10) infinite;
      pointer-events: none;
      z-index: -1;
      opacity: 0.3;
    }

    @keyframes grain {
      0%, 100% { transform: translate(0, 0); }
      10% { transform: translate(-5%, -10%); }
      30% { transform: translate(3%, -15%); }
      50% { transform: translate(12%, 9%); }
      70% { transform: translate(9%, 4%); }
      90% { transform: translate(-1%, 7%); }
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 30px;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(0, 200, 83, 0.1);
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(to right, var(--primary), var(--gold));
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(0, 200, 83, 0.1);
    }

    .header h2 {
      font-family: 'Playfair Display', serif;
      color: var(--accent);
      margin: 0;
      font-size: 28px;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .header h2 i {
      color: var(--primary);
    }

    .btn {
      padding: 12px 24px;
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      position: relative;
      overflow: hidden;
      letter-spacing: 0.5px;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: var(--transition);
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn-primary {
      background: var(--primary);
      color: var(--white);
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-3px);
      box-shadow: var(--shadow-hover);
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary-dark);
    }

    .btn-outline:hover {
      background: var(--primary);
      color: var(--white);
      transform: translateY(-3px);
      box-shadow: var(--shadow-hover);
    }

    .btn-danger {
      background: #f44336;
      color: var(--white);
      border: 2px solid transparent;
    }

    .btn-danger:hover {
      background: #d32f2f;
      transform: translateY(-3px);
      box-shadow: 0 20px 60px rgba(244, 67, 54, 0.25);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 14px;
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid rgba(0, 200, 83, 0.1);
    }

    th {
      background: rgba(0, 200, 83, 0.05);
      color: var(--primary-dark);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 0.5px;
    }

    tr:hover {
      background: rgba(0, 200, 83, 0.03);
    }

    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
      min-width: 70px;
      text-align: center;
      transition: var(--transition-fast);
    }

    .status-active {
      background: rgba(76, 175, 80, 0.1);
      color: var(--primary-dark);
    }

    .status-inactive {
      background: rgba(244, 67, 54, 0.1);
      color: #c62828;
    }

    .actions {
      display: flex;
      gap: 10px;
    }

    .alert {
      padding: 15px 20px;
      margin-bottom: 25px;
      border-radius: var(--radius-sm);
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      animation: fadeIn 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .alert.success {
      background: rgba(76, 175, 80, 0.1);
      color: var(--primary-dark);
      border-left: 4px solid var(--primary);
    }

    .alert.error {
      background: rgba(244, 67, 54, 0.1);
      color: #c62828;
      border-left: 4px solid #f44336;
    }

    .no-clients {
      text-align: center;
      padding: 40px 20px;
      color: var(--text);
    }

    .no-clients i {
      font-size: 48px;
      margin-bottom: 20px;
      color: rgba(0, 200, 83, 0.3);
    }

    .no-clients p {
      font-size: 16px;
      margin-bottom: 20px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .modal.active {
      display: flex;
      opacity: 1;
    }

    .modal-content {
      background-color: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow-hover);
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
      transform: translateY(-50px);
      transition: transform 0.3s ease;
    }

    .modal.active .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      padding: 20px 25px;
      border-bottom: 1px solid rgba(0, 200, 83, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(to right, var(--primary-light), var(--primary));
      color: white;
      border-radius: var(--radius) var(--radius) 0 0;
    }

    .modal-header h3 {
      margin: 0;
      font-family: 'Playfair Display', serif;
    }

    .close-modal {
      background: none;
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
      transition: var(--transition-fast);
    }

    .close-modal:hover {
      transform: rotate(90deg);
    }

    .modal-body {
      padding: 25px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--text);
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid rgba(0, 200, 83, 0.3);
      border-radius: var(--radius-sm);
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
      transition: var(--transition-fast);
      background-color: var(--cream);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.1);
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid rgba(0, 200, 83, 0.1);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .container {
        padding: 20px;
      }
      
      .header h2 {
        font-size: 24px;
      }
    }

    @media (max-width: 768px) {
      table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
      
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      .modal-content {
        width: 95%;
      }
    }

    @media (max-width: 576px) {
      body {
        padding: 10px;
      }
      
      .container {
        padding: 15px;
      }
      
      .btn {
        padding: 10px 15px;
        font-size: 13px;
      }
      
      th, td {
        padding: 10px 8px;
      }
      
      .modal-body {
        padding: 15px;
      }
      
      .form-actions {
        flex-direction: column;
      }
      
      .form-actions .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2><i class="fas fa-users"></i> Liste des Clients</h2>
      <div>
        <a href="inscription">
        <button class="btn btn-primary">
          <i class="fas fa-plus"></i> Nouveau Client
        </button>
        </a>
      </div>
    </div>

    <?php if (!empty($message)): ?>
      <div class="alert <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
        <i class="fas <?= strpos($message, '✅') !== false ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if (empty($clients)): ?>
      <div class="no-clients">
        <i class="fas fa-user-slash"></i>
        <p>Aucun client trouvé dans la base de données</p>
        <a href="inscription">
        <button class="btn btn-outline">
          
          <i class="fas fa-plus"></i> Ajouter un client
        
        </button>
          </a>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Inscription</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clients as $client): ?>
            <tr>
              <td><?= htmlspecialchars($client['id']) ?></td>
              <td><?= htmlspecialchars($client['nom']) ?></td>
              <td><?= htmlspecialchars($client['prenom']) ?></td>
              <td><?= htmlspecialchars($client['email']) ?></td>
              <td><?= htmlspecialchars($client['numero']) ?></td>
              <td><?= htmlspecialchars($client['date_inscription']) ?></td>
              <td>
                <span class="status-badge <?= $client['is_active'] ? 'status-active' : 'status-inactive' ?>">
                  <?= $client['is_active'] ? 'Actif' : 'Inactif' ?>
                </span>
              </td>
              <td>
                <form method="POST" style="display: inline;">
                <div class="actions">
                  <button type="button" onclick="editClient(<?= $client['id'] ?>, '<?= htmlspecialchars($client['nom']) ?>', '<?= htmlspecialchars($client['prenom']) ?>', '<?= htmlspecialchars($client['email']) ?>', '<?= htmlspecialchars($client['numero']) ?>', '<?= htmlspecialchars($client['role']) ?>', <?= $client['is_active'] ? 1 : 0 ?>)" class="btn btn-outline" title="Modifier" name="btn_Modifier">
                    <i class="fas fa-edit"></i>
                  </button>
                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                    <button type="submit" name="btn_supprimer" class="btn btn-danger" title="Supprimer">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- Modal de modification -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-user-edit"></i> Modifier le client</h3>
        <button type="button" class="close-modal" onclick="closeModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="POST">
          <input type="hidden" id="edit_id" name="id">
          
          <div class="form-group">
            <label for="sai_nom">Nom</label>
            <input type="text" id="sai_nom" name="sai_nom" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="sai_prenom">Prénom</label>
            <input type="text" id="sai_prenom" name="sai_prenom" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="sai_email">Email</label>
            <input type="email" id="sai_email" name="sai_email" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="sai_numero">Téléphone</label>
            <input type="text" id="sai_numero" name="sai_numero" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="sai_role">Rôle</label>
            <select id="sai_role" name="sai_role" class="form-control" required>
              <option value="client">Client</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn btn-outline" onclick="closeModal()">Annuler</button>
            <button type="submit" name="btn_modifier" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Fonction pour ouvrir la modale avec les données du client
    function editClient(id, nom, prenom, email, numero, role, isActive) {
      document.getElementById('edit_id').value = id;
      document.getElementById('sai_nom').value = nom;
      document.getElementById('sai_prenom').value = prenom;
      document.getElementById('sai_email').value = email;
      document.getElementById('sai_numero').value = numero;
      document.getElementById('sai_role').value = role || 'client';
      
      document.getElementById('editModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    // Fonction pour fermer la modale
    function closeModal() {
      document.getElementById('editModal').classList.remove('active');
      document.body.style.overflow = 'auto';
    }
    
    // Fermer la modale en cliquant à l'extérieur
    document.getElementById('editModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });
    
    // Animation des lignes du tableau
    document.querySelectorAll('tbody tr').forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(20px)';
      row.style.animation = `fadeIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards ${index * 0.1}s`;
    });
    
    // Fermer modal avec touche Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
      }
    });
  </script>
</body>
</html>