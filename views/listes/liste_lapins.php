<?php
// Assurez-vous d'avoir $lapins et $message disponibles depuis votre controller
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Liste des Lapins - Lapinou Store</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
  --primary: #4CAF50;
  --primary-light: #81C784;
  --primary-dark: #388E3C;
  --background: #F5F5DC;
  --text: #4E342E;
  --text-light: #8D6E63;
  --white: #ffffff;
  --shadow: 0 4px 12px rgba(0,0,0,0.1);
  --radius: 8px;
  --transition: all 0.3s ease;
}
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: var(--background);
  color: var(--text);
  margin: 0;
  padding: 20px;
  line-height: 1.6;
}
.container {
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  max-width: 1200px;
  margin: 0 auto;
  padding: 30px;
  border: 1px solid rgba(76, 175, 80, 0.2);
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid rgba(76, 175, 80, 0.1);
}
.header h2 {
  color: var(--primary-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.8rem;
}
.btn {
  padding: 10px 20px;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-primary {
  background: var(--primary);
  color: white;
  box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
}
.btn-primary:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
}
.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
}
.btn-outline {
  background: transparent;
  border: 1px solid var(--primary);
  color: var(--primary);
}
.btn-outline:hover {
  background: rgba(76, 175, 80, 0.1);
}
.btn-danger {
  background: #f44336;
  color: white;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th, td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid rgba(76, 175, 80, 0.1);
}
th {
  background: rgba(76, 175, 80, 0.05);
  font-weight: 600;
  color: var(--primary-dark);
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.5px;
}
tr:hover {
  background: rgba(76, 175, 80, 0.03);
}
.product-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius);
  border: 1px solid rgba(76, 175, 80, 0.2);
  transition: var(--transition);
}
.product-image:hover {
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.status-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
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
  gap: 8px;
}
.alert {
  padding: 15px;
  margin-bottom: 20px;
  border-radius: var(--radius);
  font-weight: 500;
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
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(3px);
}
.modal-content {
  background: var(--white);
  margin: 5% auto;
  padding: 30px;
  border-radius: var(--radius);
  width: 90%;
  max-width: 550px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  border: 1px solid var(--primary);
  position: relative;
}
.close {
  color: var(--text-light);
  position: absolute;
  right: 20px;
  top: 20px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  transition: var(--transition);
}
.close:hover {
  color: var(--primary-dark);
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: var(--text);
}
.form-group input, 
.form-group textarea, 
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid rgba(76, 175, 80, 0.3);
  border-radius: var(--radius);
  font-size: 14px;
  transition: var(--transition);
  background: var(--white);
  color: var(--text);
}
.form-group input:focus, 
.form-group textarea:focus, 
.form-group select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
}
.form-group textarea {
  min-height: 100px;
  resize: vertical;
}
.form-checks {
  display: flex;
  gap: 20px;
  align-items: center;
  margin-bottom: 15px;
}
.no-lapins {
  text-align: center;
  color: var(--text-light);
  margin: 50px 0;
  padding: 20px;
  background: rgba(76, 175, 80, 0.05);
  border-radius: var(--radius);
  border: 1px dashed rgba(76, 175, 80, 0.3);
}
.no-lapins i {
  font-size: 48px;
  margin-bottom: 15px;
  display: block;
  color: rgba(76, 175, 80, 0.3);
}
.no-lapins a {
  color: var(--primary-dark);
  text-decoration: none;
  font-weight: 600;
}
.no-lapins a:hover {
  text-decoration: underline;
}
@media (max-width: 768px) {
  .container { padding: 15px; }
  th, td { padding: 8px 10px; font-size: 0.9rem; }
  .header { flex-direction: column; gap: 15px; align-items: flex-start; }
  .actions { flex-direction: column; gap: 5px; }
}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h2><i class="fas fa-paw" style="color: var(--primary)"></i> Liste des lapins</h2>
    <a href="ajouterLapin" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter un lapin</a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($lapins)): ?>
    <div class="no-lapins">
      <i class="fas fa-paw"></i> Aucun lapin trouvé. <a href="ajouterLapin">Ajouter le premier lapin</a>
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Nom</th>
          <th>Race</th>
          <th>Âge</th>
          <th>Poids</th>
          <th>Prix</th>
          <th>Stock</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lapins as $lapin): ?>
        <tr>
          <td>
            <?php if (!empty($lapin['photo'])): ?>
              <img src="<?= htmlspecialchars($lapin['photo']) ?>" alt="<?= htmlspecialchars($lapin['nom']) ?>" class="product-image">
            <?php else: ?>
              <div style="width:60px;height:60px;background:rgba(76,175,80,0.05);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;border:1px dashed rgba(76,175,80,0.3)">
                <i class="fas fa-paw" style="color: rgba(76,175,80,0.3)"></i>
              </div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($lapin['nom']) ?></td>
          <td><?= htmlspecialchars($lapin['race'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($lapin['age_mois'] ?? 'N/A') ?> mois</td>
          <td><?= htmlspecialchars($lapin['poids_kg'] ?? 'N/A') ?> kg</td>
          <td><?= number_format($lapin['prix'],0,',',' ') ?> FCFA</td>
          <td><?= htmlspecialchars($lapin['stock']) ?></td>
          <td>
            <span class="status-badge <?= $lapin['is_active'] ? 'status-active' : 'status-inactive' ?>">
              <?= $lapin['is_active'] ? 'Actif' : 'Inactif' ?>
            </span>
          </td>
          <td class="actions">
            <button onclick='editLapin(<?= htmlspecialchars(json_encode($lapin), ENT_QUOTES, "UTF-8") ?>)' class="btn btn-sm btn-outline">
              <i class="fas fa-edit"></i>
            </button>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce lapin ?')">
              <input type="hidden" name="id" value="<?= $lapin['id'] ?>">
              <button type="submit" name="btn_supprimer" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Modal édition -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 style="color: var(--primary-dark); margin-bottom: 20px; display:flex;align-items:center;gap:10px;"><i class="fas fa-edit"></i> Modifier le lapin</h3>
    <form method="POST" id="editForm">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-group"><label for="edit_name">Nom</label><input type="text" name="name" id="edit_name" required></div>
      <div class="form-group"><label for="edit_breed">Race</label><input type="text" name="breed" id="edit_breed" required></div>
      <div class="form-group"><label for="edit_age_months">Âge (mois)</label><input type="number" name="age_months" id="edit_age_months" min="1" required></div>
      <div class="form-group"><label for="edit_weight_kg">Poids (kg)</label><input type="number" name="weight_kg" id="edit_weight_kg" step="0.1" min="0.1" required></div>
      <div class="form-group"><label for="edit_price">Prix (FCFA)</label><input type="number" name="price" id="edit_price" min="0" required></div>
      <div class="form-group"><label for="edit_stock_quantity">Stock</label><input type="number" name="stock_quantity" id="edit_stock_quantity" min="0" required></div>
      <div class="form-group"><label for="edit_description">Description</label><textarea name="description" id="edit_description" required></textarea></div>
      <div class="form-group"><label for="edit_image_url">URL Image</label><input type="url" name="image_url" id="edit_image_url"></div>
      <div class="form-checks">
        <label><input type="checkbox" name="is_featured" id="edit_is_featured"> En vedette</label>
        <label><input type="checkbox" name="is_active" id="edit_is_active"> Actif</label>
      </div>
      <button type="submit" name="btn_modifier" class="btn btn-primary" style="width:100%;padding:12px;margin-top:10px"><i class="fas fa-save"></i> Enregistrer les modifications</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('editModal');
modal.querySelector('.close').onclick = () => modal.style.display='none';
window.onclick = e => { if(e.target==modal) modal.style.display='none'; };

function editLapin(lapin){
  modal.style.display='block';
  document.getElementById('edit_id').value = lapin.id;
  document.getElementById('edit_name').value = lapin.nom;
  document.getElementById('edit_breed').value = lapin.race;
  document.getElementById('edit_age_months').value = lapin.age_mois;
  document.getElementById('edit_weight_kg').value = lapin.poids_kg;
  document.getElementById('edit_price').value = lapin.prix;
  document.getElementById('edit_stock_quantity').value = lapin.stock;
  document.getElementById('edit_description').value = lapin.description;
  document.getElementById('edit_image_url').value = lapin.photo || '';
  document.getElementById('edit_is_featured').checked = lapin.is_featured==1;
  document.getElementById('edit_is_active').checked = lapin.is_active==1;
}
</script>
</body>
</html>
