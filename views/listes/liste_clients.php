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

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--light);
      color: var(--text);
      padding: 20px;
    }

    .container {
      max-width: 1400px;
      margin: auto;
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 25px;
      border: 1px solid rgba(0, 200, 83, 0.1);
      position: relative;
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      height: 5px;
      width: 100%;
      background: linear-gradient(to right, var(--primary), var(--gold));
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      border-bottom: 1px solid rgba(0, 200, 83, 0.1);
      padding-bottom: 15px;
    }

    .header h2 { font-family: 'Playfair Display', serif; font-size: 26px; }
    .btn {
      padding: 10px 18px;
      border-radius: var(--radius-sm);
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 13px;
      transition: var(--transition-fast);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); }

    .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary-dark); }
    .btn-outline:hover { background: var(--primary); color: white; }

    .btn-danger { background: #f44336; color: white; }
    .btn-danger:hover { background: #c62828; }

    table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 20px; }
    th, td { padding: 12px 15px; border-bottom: 1px solid rgba(0, 200, 83, 0.1); }
    th { background: rgba(0, 200, 83, 0.05); text-transform: uppercase; font-size: 12px; }

    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }
    .status-active { background: rgba(76,175,80,0.1); color: var(--primary-dark); }
    .status-inactive { background: rgba(244,67,54,0.1); color: #c62828; }

    .actions-bar {
      display: flex;
      gap: 10px;
    }

    .edit-form-container {
      display: none;
      background: rgba(0, 200, 83, 0.03);
    }
    .edit-form-container.open {
      display: table-row;
      animation: fadeIn 0.4s ease-out forwards;
    }

    .edit-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      padding: 15px;
      border-left: 4px solid var(--primary);
      border-radius: var(--radius-sm);
    }

    .form-actions {
      grid-column: 1 / -1;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      border-top: 1px solid rgba(0,200,83,0.1);
      padding-top: 12px;
      margin-top: 10px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .no-clients { text-align: center; padding: 40px; }
  </style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2><i class="fas fa-users" style="color:var(--primary)"></i> Liste des Clients</h2>
    <a href="inscription">
      <button class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau client</button>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($clients)): ?>
    <div class="no-clients no-clients">
      <i class="fas fa-user-slash"></i>
      <p>Aucun client trouvé</p>
      <a href="inscription"><button class="btn btn-outline"><i class="fas fa-plus"></i>Ajouter un client</button></a>
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
          <th>Mot de passe</th>
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
          <td>••••••••</td>

          <td>
            <div class="actions-bar">
              <button type="button" onclick="toggleEditForm(<?= $client['id'] ?>)" class="btn btn-outline"><i class="fas fa-edit"></i>Modifier</button>

              <form method="POST">
                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                <button type="submit" name="btn_supprimer" class="btn btn-danger"><i class="fas fa-trash"></i>Supprimer</button>
              </form>
            </div>
          </td>
        </tr>

        <tr id="edit-form-<?= $client['id'] ?>" class="edit-form-container">
          <td colspan="9">
            <form class="edit-form" method="POST">
              <input type="hidden" name="id" value="<?= $client['id'] ?>">

              <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
              </div>

              <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($client['prenom']) ?>" required>
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
              </div>

              <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($client['numero']) ?>" required>
              </div>

              <div class="form-group">
                <label>Nouveau mot de passe</label>
                <input type="password" name="new_password" class="form-control" placeholder="Vide = pas de changement">
              </div>

              <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="toggleEditForm(<?= $client['id'] ?>)">Annuler</button>
                <button type="submit" name="btn_modifier" class="btn btn-primary">Enregistrer</button>
              </div>
            </form>
          </td>
        </tr>

      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script>
function toggleEditForm(id) {
  const row = document.getElementById(`edit-form-${id}`);
  row.classList.toggle("open");
}
</script>

</body>
</html>
