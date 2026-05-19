#!/bin/bash

# Installer les dépendances si le dossier vendor n'existe pas
if [ ! -d "vendor" ]; then
    echo "Installation des dépendances Composer..."
    composer install --no-interaction
fi

# Créer la base de données SQLite si elle n'existe pas
if [ ! -f "database/database.sqlite" ]; then
    echo "Création de la base de données SQLite..."
    touch database/database.sqlite
    php artisan migrate:fresh --seed --force
fi

# Démarrer le serveur PHP embarqué pour le développement
echo "Démarrage du serveur de développement Laravel..."
php artisan serve --host=0.0.0.0 --port=8000
