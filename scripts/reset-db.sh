#!/bin/bash

# Script pour réinitialiser complètement la base de données
echo "🔄 Réinitialisation complète de la base de données..."

echo "⚠️  ATTENTION: Cette opération va supprimer toutes les données!"
read -p "Êtes-vous sûr de vouloir continuer? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Opération annulée."
    exit 1
fi

# Arrêter les conteneurs
echo "🛑 Arrêt des conteneurs..."
docker-compose down

# Supprimer le volume de données PostgreSQL
echo "🗑️  Suppression du volume de données..."
docker volume rm appwoyofal2_postgres_data 2>/dev/null || true

# Redémarrer les conteneurs
echo "🚀 Redémarrage des conteneurs..."
docker-compose up -d

# Attendre que PostgreSQL soit prêt
echo "⏳ Attente du démarrage de PostgreSQL..."
sleep 10

# Exécuter les migrations
echo "📊 Exécution des migrations..."
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -f /docker-entrypoint-initdb.d/001_create_tables.sql

# Exécuter les seeders
echo "🌱 Insertion des données de test..."
docker cp seeders/test_data.sql woyofal_postgres:/tmp/test_data.sql
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -f /tmp/test_data.sql

echo "✅ Base de données réinitialisée avec succès!"

# Tester l'API
echo "🧪 Test de l'API..."
sleep 2
curl -s http://localhost:8082/index.php?/api/health | jq .status || echo "API pas encore prête"
