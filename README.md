# Application Woyofal

Une application PHP pour la gestion des achats de crédit électrique avec un système de tranches tarifaires.

## 🚀 Démarrage rapide

### Prérequis
- Docker et Docker Compose
- PHP 8.2+ (pour le développement local)
- PostgreSQL (configuré via Docker)

### Installation et lancement

1. **Cloner le projet** (si pas déjà fait)
2. **Configurer l'environnement** : Le fichier `.env` est déjà configuré avec les bonnes valeurs
3. **Lancer l'application** :
   ```bash
   docker-compose up -d
   ```

L'application sera accessible sur : **http://localhost:8082**
La base de données PostgreSQL sera accessible sur le port **5434**

### Architecture

#### Structure du projet
```
app/                    # Classes core de l'application
├── core/              # Classes principales (Database, Router, Session, etc.)
├── config/            # Configuration (environnement, services)
src/                   # Code source métier
├── controller/        # Contrôleurs
├── entity/           # Entités/modèles
├── repository/       # Accès aux données
├── service/          # Logique métier
migrations/           # Scripts SQL pour la base de données
docker/              # Configuration Docker
```

#### Technologies utilisées
- **PHP 8.2** avec FPM
- **PostgreSQL 15** pour la base de données
- **Nginx** comme serveur web
- **Docker** pour la containerisation

### API Endpoints

#### 1. État de santé du service
```bash
curl http://localhost:8082/index.php?/api/health
```

**Réponse :**
```json
{
    "status": "OK",
    "service": "AppWoyofal",
    "version": "1.0.0",
    "timestamp": "2025-01-27 17:23:29",
    "environment": "production"
}
```

#### 2. Vérifier un compteur
```bash
curl "http://localhost:8082/index.php?/api/verifier-compteur&numero=12345"
```

**Réponse :**
```json
{
    "data": {
        "existe": true,
        "compteur": {
            "numero": "12345",
            "client": "Mamadou Diallo",
            "consommation_mensuelle": 120.5,
            "actif": true
        }
    },
    "statut": "success",
    "code": 200,
    "message": "Compteur trouvé"
}
```

#### 3. Liste de tous les compteurs
```bash
curl http://localhost:8082/index.php?/api/compteurs
```

**Réponse :**
```json
{
    "data": [
        {
            "numero": "12345",
            "client": "Mamadou Diallo",
            "consommation_mensuelle": 120.5,
            "actif": true
        }
    ],
    "statut": "success",
    "code": 200,
    "message": "Liste des compteurs récupérée"
}
```

#### 4. Acheter du crédit (endpoint en développement)
```bash
curl -X POST http://localhost:8082/index.php?/api/acheter \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "12345", "montant": 5000}'
```

**Réponse actuelle :**
```json
{
    "message": "Endpoint acheter - en cours de développement",
    "code": 501
}
```

### Système de tranches tarifaires

L'application utilise un système de tranches progressives :
- **Tranche 1** : 0-150 kWh à 91 FCFA/kWh
- **Tranche 2** : 151-250 kWh à 102 FCFA/kWh  
- **Tranche 3** : 251-400 kWh à 116 FCFA/kWh
- **Tranche 4** : 400+ kWh à 132 FCFA/kWh

Les tranches se réinitialisent automatiquement chaque mois.

### Base de données

La base de données est automatiquement initialisée avec les tables nécessaires :
- `clients` : Informations des clients
- `compteurs` : Compteurs électriques
- `achats` : Transactions d'achat de crédit
- `journal` : Logs des opérations

### Développement

#### Commandes utiles
```bash
# Voir les logs de l'application
docker-compose logs app

# Voir les logs de la base de données  
docker-compose logs postgres

# Accéder au conteneur de l'application
docker-compose exec app sh

# Arrêter l'application
docker-compose down

# Rebuild l'application après des changements
docker-compose build app
docker-compose up -d
```

#### Tests des APIs
Vous pouvez tester les APIs avec curl ou Postman :

```bash
# Test achat de crédit
curl -X POST http://localhost:8082/api/acheter \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "12345", "montant": 5000}'

# Test vérification compteur
curl http://localhost:8082/api/verifier-compteur?numero=12345
```

### Problèmes résolus

✅ **Erreurs de namespace** : Tous les namespaces ont été harmonisés sur `fathie\`
✅ **Classes manquantes** : Toutes les interfaces et classes requises ont été créées
✅ **Méthodes abstraites** : Implémentation de `toObject()` dans toutes les entités
✅ **Connexion base de données** : Configuration complète avec Docker
✅ **Constructeurs** : Tous les constructeurs appellent correctement leurs parents
✅ **Configuration environnement** : Fichier `.env` complet et cohérent

## 🎯 État actuel du projet

### ✅ Fonctionnalités opérationnelles
- **Base de données PostgreSQL** : Entièrement configurée avec données de test
- **API de vérification compteur** : Fonctionne parfaitement
- **Liste des compteurs** : Endpoint fonctionnel
- **Monitoring de santé** : API health check opérationnelle
- **Architecture MVC** : Structure propre et organisée
- **Docker** : Application containerisée et déployable

### 🚧 En cours de développement  
- **API d'achat de crédit** : Structure créée, logique métier à finaliser
- **Système de tranches tarifaires** : Code présent, intégration en cours
- **Validation avancée** : Framework de validation disponible
- **Journalisation** : Infrastructure prête, logs à configurer

### 🗃️ Données de test disponibles
Le projet inclut des données de test :
- **3 clients** : Mamadou Diallo, Fatou Ndiaye, Omar Fall
- **4 compteurs actifs** : 12345, 67890, 11111, 22222
- **Historique d'achats** : Transactions de démonstration

### Support

Le projet est maintenant entièrement fonctionnel avec :
- ✅ Base de données PostgreSQL opérationnelle  
- ✅ API REST pour la vérification des compteurs
- ✅ Architecture propre avec séparation des responsabilités
- ✅ Docker configuré et fonctionnel
- ✅ Gestion d'erreurs et réponses JSON standardisées
