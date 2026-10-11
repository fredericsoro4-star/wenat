# 🐰 Lapinou Store - Système de Gestion d'Élevage de Lapins

![Version](https://img.shields.io/badge/version-1.0.0-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![License](https://img.shields.io/badge/license-MIT-red.svg)

## 📋 Table des Matières

- [Description](#description)
- [Fonctionnalités](#fonctionnalités)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [API](#api)
- [Base de Données](#base-de-données)
- [Sécurité](#sécurité)
- [Contributions](#contributions)

## 🎯 Description

**Lapinou Store** est un système complet de gestion d'élevage de lapins développé en PHP avec une architecture MVC. Il permet de gérer l'inventaire des lapins, les commandes, les clients, et offre une interface e-commerce moderne pour la vente en ligne.

### 🎨 Capture d'écran de l'interface

- **Interface moderne et responsive** - Compatible mobile et desktop
- **Design professionnel** - Utilisant CSS3 et animations fluides
- **UX optimisée** - Navigation intuitive et feedback utilisateur

## ✨ Fonctionnalités

### 🔐 Gestion des Utilisateurs
- ✅ Inscription/Connexion sécurisée
- ✅ Multi-rôles (Admin, Client, Employé, Livreur)
- ✅ Gestion des profils
- ✅ Réinitialisation de mot de passe

### 🐰 Gestion des Lapins
- ✅ CRUD complet (Créer, Lire, Mettre à jour, Supprimer)
- ✅ Informations détaillées (race, âge, poids, prix)
- ✅ Gestion des catégories
- ✅ Upload d'images
- ✅ Suivi du stock en temps réel
- ✅ Système de recherche et filtrage

### 🛒 E-commerce
- ✅ Catalogue public moderne
- ✅ Panier d'achat persistant
- ✅ Système de commandes complet
- ✅ Gestion des adresses de livraison
- ✅ Historique des commandes

### 💳 Paiements
- ✅ Support multi-méthodes de paiement
  - Mobile Money (Wave, Orange Money, Moov Money, MTN)
  - Cartes bancaires
  - Paiement à la livraison
- ✅ Interface de paiement sécurisée

### 📊 Administration
- ✅ Dashboard avec statistiques avancées
- ✅ Gestion des utilisateurs
- ✅ Gestion des commandes
- ✅ Interface de facturation
- ✅ Suivi des livraisons

### 🚚 Livraison
- ✅ Interface dédiée aux livreurs
- ✅ Suivi des livraisons en temps réel
- ✅ Gestion des statuts de livraison

## 🏗️ Architecture

### Structure MVC
```
lapinou-store/
├── 📁 controllers/          # Contrôleurs MVC
│   ├── AdministrateurController.php
│   ├── ClientController.php
│   └── LapinController.php
├── 📁 Models/              # Modèles de données
│   ├── administrateur.php
│   ├── clients.php
│   └── produits.php
├── 📁 views/               # Vues et templates
│   ├── ajoute_lapin.php
│   ├── catalog.php
│   ├── dashboard.php
│   ├── facture.php
│   ├── liste_lapins.php
│   ├── livreur.html
│   ├── payement.php
│   └── vente.html
├── 📁 database/            # Base de données
│   ├── connexion.php
│   ├── lapin.sql
│   └── lapinou_store_complete.sql
├── 📁 config/              # Configuration
│   ├── config.php
│   └── Api.php
├── 📁 assets/              # Ressources statiques
│   ├── css/
│   ├── js/
│   └── images/
├── index.php               # Point d'entrée principal
├── router.php              # Routeur alternatif
└── README.md               # Documentation
```

### Patterns Utilisés
- **MVC (Model-View-Controller)** - Séparation claire des préoccupations
- **Active Record** - Pour l'accès aux données
- **Front Controller** - Routage centralisé
- **Dependency Injection** - Gestionnaire de dépendances

## 🚀 Installation

### Prérequis
- **PHP 8.0+** avec extensions PDO, JSON
- **MySQL 8.0+** ou MariaDB 10.4+
- **Serveur web** (Apache/Nginx)
- **Composer** (optionnel)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-username/lapinou-store.git
cd lapinou-store
```

2. **Configuration du serveur web**
```apache
# Apache (.htaccess déjà configuré)
DocumentRoot /path/to/lapinou-store
```

3. **Créer la base de données**
```sql
-- Importer le fichier SQL complet
mysql -u root -p < database/lapinou_store_complete.sql
```

4. **Configuration de la base de données**
```php
// database/connexion.php
$host = 'localhost';
$dbname = 'lapinou_store';
$username = 'votre_username';
$password = 'votre_password';
```

5. **Permissions des dossiers**
```bash
chmod 755 uploads/
chmod 755 logs/
```

## ⚙️ Configuration

### Configuration principale
Éditez le fichier [`config/config.php`](config/config.php) :

```php
// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'lapinou_store');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application
define('APP_NAME', 'Lapinou Store');
define('APP_URL', 'http://localhost/projets/lapin/');

// Sécurité
define('SESSION_DURATION', 3600);
define('DEBUG_MODE', false); // false en production
```

### Configuration email
Pour les notifications :
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'votre_email@gmail.com');
define('SMTP_PASSWORD', 'votre_mot_de_passe_app');
```

## 🎮 Utilisation

### Accès aux interfaces

#### 🏠 Page d'accueil publique
```
http://localhost/projets/lapin/
```
- Catalogue des lapins
- Recherche et filtres
- Panier d'achat

#### 👨‍💼 Dashboard Administrateur
```
http://localhost/projets/lapin/?action=dashboard
```
**Compte par défaut :**
- Email: `admin@lapinou-store.com`
- Mot de passe: `password`

**Fonctionnalités :**
- Vue d'ensemble des statistiques
- Gestion des lapins
- Gestion des commandes
- Gestion des utilisateurs

#### 🐰 Gestion des Lapins
```
# Ajouter un lapin
http://localhost/projets/lapin/?action=ajouter_lapin

# Liste des lapins
http://localhost/projets/lapin/?action=liste_lapins

# Catalogue public
http://localhost/projets/lapin/?action=catalog
```

#### 🛒 E-commerce
```
# Inscription client
http://localhost/projets/lapin/?action=inscription

# Connexion
http://localhost/projets/lapin/?action=connexion

# Panier
http://localhost/projets/lapin/?action=panier
```

#### 🚚 Interface Livreur
```
http://localhost/projets/lapin/?action=livreur
```

### Workflows Principaux

#### 📋 Ajouter un Lapin
1. Aller sur "Ajouter un lapin"
2. Remplir le formulaire :
   - Nom et race
   - Âge et poids
   - Prix et stock
   - Description
   - Image (URL)
3. Valider le formulaire

#### 🛍️ Processus de Commande (Client)
1. S'inscrire/Se connecter
2. Naviguer dans le catalogue
3. Ajouter des lapins au panier
4. Voir le panier et modifier si nécessaire
5. Passer commande
6. Choisir méthode de paiement
7. Confirmer la commande

#### 📦 Gestion des Commandes (Admin)
1. Voir les nouvelles commandes
2. Mettre à jour le statut
3. Assigner une livraison
4. Suivre jusqu'à la livraison

## 🔌 API

### Endpoints Disponibles

#### Lapins
```php
// Récupérer tous les lapins (JSON)
GET /?action=api_lapins

// Récupérer un lapin spécifique
GET /?action=api_lapin&id=1

// Mettre à jour le stock
POST /?action=api_update_stock
{
  "rabbit_id": 1,
  "new_stock": 10
}
```

#### Réponses API
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Blanc Nouvelle Zélande",
      "race": "Nouvelle Zélande",
      "prix": 15000,
      "stock": 5
    }
  ]
}
```

## 🗄️ Base de Données

### Schéma Principal

#### Tables Principales
- **`users`** - Utilisateurs du système
- **`rabbits`** - Inventaire des lapins
- **`orders`** - Commandes clients
- **`order_items`** - Détails des commandes
- **`cart_items`** - Panier d'achat
- **`categories`** - Catégories de lapins

#### Schéma Détaillé
Consultez le fichier [`database/lapinou_store_complete.sql`](database/lapinou_store_complete.sql) pour :
- Structure complète des tables
- Relations et contraintes
- Index de performance
- Données d'exemple
- Vues et procédures stockées

### Scripts SQL Utiles

```sql
-- Statistiques des ventes
SELECT 
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(total_amount) as revenue
FROM orders 
WHERE status = 'delivered'
GROUP BY DATE(created_at);

-- Lapins les plus vendus
SELECT 
    r.name,
    SUM(oi.quantity) as total_sold
FROM rabbits r
JOIN order_items oi ON r.rabbit_id = oi.rabbit_id
JOIN orders o ON oi.order_id = o.order_id
WHERE o.status IN ('delivered', 'shipped')
GROUP BY r.rabbit_id
ORDER BY total_sold DESC;
```

## 🔒 Sécurité

### Mesures Implémentées

#### 🛡️ Protection des Données
- **Hachage des mots de passe** avec `password_hash()`
- **Requêtes préparées** contre l'injection SQL
- **Échappement HTML** avec `htmlspecialchars()`
- **Validation côté serveur** de tous les inputs

#### 🔐 Authentification
- **Sessions sécurisées** avec timeout
- **Protection CSRF** sur les formulaires
- **Limitation des tentatives** de connexion
- **Tokens de vérification** pour l'email

#### 🌐 Sécurité Web
- **Headers de sécurité** configurés
- **HTTPS recommandé** en production
- **Protection XSS** sur tous les outputs
- **Validation des uploads** de fichiers

### Configuration de Sécurité

```php
// config/config.php
define('SESSION_DURATION', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('DEBUG_MODE', false); // JAMAIS true en production

// En production, ajoutez :
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
```

## 🧪 Tests

### Tests Manuels Recommandés

#### Fonctionnalités de Base
- [ ] Connexion administrateur
- [ ] Ajout d'un lapin
- [ ] Modification d'un lapin
- [ ] Suppression d'un lapin
- [ ] Recherche dans le catalogue
- [ ] Inscription client
- [ ] Ajout au panier
- [ ] Processus de commande

#### Tests de Sécurité
- [ ] Tentative d'injection SQL
- [ ] Accès non autorisé aux pages admin
- [ ] Upload de fichiers malveillants
- [ ] Validation des formulaires

## 📱 Responsivité

Le système est entièrement **responsive** et s'adapte à :
- 📱 **Mobile** (320px - 768px)
- 📱 **Tablette** (768px - 1024px)
- 💻 **Desktop** (1024px+)

## 🚀 Déploiement en Production

### Checklist de Déploiement

#### Configuration
- [ ] `DEBUG_MODE = false`
- [ ] Mots de passe sécurisés
- [ ] HTTPS activé
- [ ] Sauvegarde de la base de données

#### Sécurité
- [ ] Permissions des fichiers correctes
- [ ] Accès aux dossiers sensibles bloqué
- [ ] Logs d'erreurs configurés
- [ ] Monitoring activé

### Optimisations Recommandées

```apache
# .htaccess optimisations
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript
</IfModule>

<IfModule mod_expires.c>
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

## 🤝 Contributions

### Comment Contribuer
1. **Fork** le projet
2. **Créer** une branche feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** les changements (`git commit -m 'Add some AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrir** une Pull Request

### Standards de Code
- **PSR-12** pour PHP
- **Commentaires** en français
- **Variables** en français
- **Tests** pour les nouvelles fonctionnalités

## 📞 Support

### Contacts
- **Développeur** : Soro Songui Dognimé Frédéric
- **Email** : fredericuchiwa52@gmail.com
- **GitHub** : [Votre profil GitHub]

### Problèmes Courants

#### ❌ Erreur de connexion base de données
```
Solution : Vérifiez database/connexion.php
```

#### ❌ Page blanche
```
Solution : Activez DEBUG_MODE et vérifiez les logs
```

#### ❌ Problème de permissions
```bash
chmod 755 uploads/
chmod 755 logs/
```

## 📄 License

Ce projet est sous licence **MIT**. Voir le fichier `LICENSE` pour plus de détails.

---

## 🎉 Remerciements

Merci de choisir **Lapinou Store** pour votre projet d'élevage de lapins !

### Technologies Utilisées
- **PHP 8+** - Backend
- **MySQL 8+** - Base de données
- **HTML5/CSS3** - Frontend
- **JavaScript** - Interactivité
- **Font Awesome** - Icônes
- **Bootstrap concepts** - Grid system

### Fonctionnalités Avancées Possibles
- 📊 **Reporting avancé** avec graphiques
- 📧 **Notifications email** automatiques
- 🔄 **Synchronisation** avec APIs externes
- 📱 **Application mobile** (React Native/Flutter)
- 🤖 **Chatbot** d'assistance client
- 📈 **Analytics** détaillées

---

**Version :** 1.0.0 | **Dernière mise à jour :** Janvier 2025

> 💡 **Conseil :** Consultez le fichier [`PLAN_AMELIORATIONS.md`](PLAN_AMELIORATIONS.md) pour un aperçu technique détaillé du développement.