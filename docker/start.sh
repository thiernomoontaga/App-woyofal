#!/bin/sh

# Attendre que la base de données soit prête
echo "Attente de la base de données..."
sleep 10

# Exécuter les migrations si nécessaire
if [ -f "/var/www/html/migrations/migration.php" ]; then
    echo "Exécution des migrations..."
    php /var/www/html/migrations/migration.php
fi

# Exécuter les seeders si nécessaire
if [ -f "/var/www/html/seeders/seeder.php" ]; then
    echo "Exécution des seeders..."
    php /var/www/html/seeders/seeder.php
fi

# Démarrer Supervisor
echo "Démarrage des services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
