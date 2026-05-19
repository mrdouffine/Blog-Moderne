#!/bin/sh

# Installer les dépendances si le dossier node_modules n'existe pas
if [ ! -d "node_modules" ]; then
    echo "Installation des dépendances NPM..."
    npm install --legacy-peer-deps
fi

# Lancer le serveur de développement Nuxt
echo "Démarrage du serveur Nuxt..."
npm run dev
