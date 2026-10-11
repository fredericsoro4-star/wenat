<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des livreurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Votre CSS existant */
        :root {
      --primary: #4CAF50;
      --primary-dark: #388E3C;
      --light: #f8faf8;
      --white: #ffffff;
      --shadow: 0 2px 6px rgba(0,0,0,0.1);
      --radius: 6px;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--light);
      margin: 0;
      padding: 15px;
      font-size: 14px;
    }

    .container {
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(76, 175, 80, 0.1);
    }

    .header h2 {
      color: var(--primary-dark);
      margin: 0;
      font-size: 1.4rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn {
      padding: 8px 15px;
      border-radius: var(--radius);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }

    .btn-danger {
      background: #f44336;
      color: white;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 13px;
    }

    th, td {
      padding: 10px 12px;
      text-align: left;
      border-bottom: 1px solid rgba(76, 175, 80, 0.1);
    }

    th {
      background: rgba(76, 175, 80, 0.05);
      color: var(--primary-dark);
      font-size: 0.75rem;
    }

    .status-badge {
      padding: 3px 8px;
      border-radius: 10px;
      font-size: 11px;
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
      gap: 5px;
    }

    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: var(--radius);
      font-size: 13px;
    }

    .alert.success {
      background: rgba(76, 175, 80, 0.1);
      color: var(--primary-dark);
      border-left: 3px solid var(--primary);
    }

    .alert.error {
      background: rgba(244, 67, 54, 0.1);
      color: #c62828;
      border-left: 3px solid #f44336;
    }

    .no-clients {
      text-align: center;
      padding: 20px;
      color: #666;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-users"></i> Liste des livreurs</h2>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($livreurs)): ?>
            <div class="no-livreurs">
                <i class="fas fa-user-slash" style="font-size: 24px; margin-bottom: 10px;"></i>
                <p>Aucun livreur trouvé</p>
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
                        <th>Date Inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($livreurs as $livreur): ?>
                        <tr>
                            <td><?= htmlspecialchars($livreur['id']) ?></td>
                            <td><?= htmlspecialchars($livreur['nom']) ?></td>
                            <td><?= htmlspecialchars($livreur['prenom']) ?></td>
                            <td><?= htmlspecialchars($livreur['email']) ?></td>
                            <td><?= htmlspecialchars($livreur['numero']) ?></td>
                            <td><?= htmlspecialchars($livreur['date_inscription']) ?></td>
                            <td>
                                <span class="status-badge <?= $livreur['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $livreur['is_active'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button onclick="editLivreur(<?= $livreur['id'] ?>)" class="btn btn-outline">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $livreur['id'] ?>">
                                        <button type="submit" name="btn_supprimer" class="btn btn-danger">
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

    <script>
        function editLivreur(id) {
            // Implémentez la logique d'édition ici
            console.log("Édition du livreur ID:", id);
        }
    </script>
</body>
</html>