#!/bin/bash
set -e
echo "⏳ Attente de la disponibilité de la base de données..."

until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  >&2 echo "❌ Base de données non disponible."
  sleep 2
done
php bin/console d:s:u --force --no-interaction
php bin/console cache:clear --no-warmup
php bin/console cache:warmup
php bin/console assets:install public --symlink

chown -R www-data:www-data /var/www/public
chmod -R 755 /var/www/public
echo "✅ Script docker.sh terminé avec succès"
exec apache2-foreground
