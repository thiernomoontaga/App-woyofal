#!/bin/bash

# Script pour exécuter les seeders (données de test)
echo "🌱 Exécution des seeders..."

# Vérifier si Docker Compose est en cours d'exécution
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Les conteneurs Docker ne sont pas en cours d'exécution."
    echo "Démarrage avec: docker-compose up -d"
    docker-compose up -d
    sleep 5
fi

# Copier et exécuter les seeders
echo "📝 Insertion des données de test..."
docker cp seeders/test_data.sql woyofal_postgres:/tmp/test_data.sql
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -f /tmp/test_data.sql

echo "✅ Seeders terminés!"

# Afficher le résumé des données
echo "📊 Résumé des données:"
docker-compose exec postgres psql -U woyofal_user -d woyofal_db -c "
SELECT 'Clients' as table_name, COUNT(*) as count FROM clients
UNION ALL
SELECT 'Compteurs', COUNT(*) FROM compteurs  
UNION ALL
SELECT 'Achats', COUNT(*) FROM achats
UNION ALL
SELECT 'Journal', COUNT(*) FROM journal;
"
