#!/bin/bash

# Script pour exécuter les migrations
echo "🔄 Exécution des migrations..."

# Vérifier si Docker Compose est en cours d'exécution
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Les conteneurs Docker ne sont pas en cours d'exécution."
    echo "Démarrage avec: docker-compose up -d"
    docker-compose up -d
    sleep 5
fi

# Exécuter les migrations
echo "📊 Création/mise à jour des tables..."
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -f /docker-entrypoint-initdb.d/001_create_tables.sql

echo "✅ Migrations terminées!"

# Afficher l'état des tables
echo "📋 Tables créées:"
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -c "\dt"
