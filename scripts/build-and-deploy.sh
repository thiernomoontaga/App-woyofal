#!/bin/bash

# Script de build et déploiement sur DockerHub
set -e

# Variables
IMAGE_NAME="appwoyofal"
DOCKER_USERNAME="brahim1205"
VERSION="1.0.0"
FULL_IMAGE_NAME="$DOCKER_USERNAME/$IMAGE_NAME:$VERSION"
LATEST_IMAGE_NAME="$DOCKER_USERNAME/$IMAGE_NAME:latest"

echo "🚀 Début du processus de build et déploiement..."

# 1. Build de l'image Docker
echo "📦 Construction de l'image Docker..."
docker build -t $IMAGE_NAME:$VERSION .
docker build -t $IMAGE_NAME:latest .

# 2. Tag des images pour DockerHub
echo "🏷️  Tagging des images..."
docker tag $IMAGE_NAME:$VERSION $FULL_IMAGE_NAME
docker tag $IMAGE_NAME:latest $LATEST_IMAGE_NAME

# 3. Connexion à DockerHub (si pas déjà connecté)
echo "🔐 Connexion à DockerHub..."
docker login

# 4. Push vers DockerHub
echo "⬆️  Push vers DockerHub..."
docker push $FULL_IMAGE_NAME
docker push $LATEST_IMAGE_NAME

echo "✅ Déploiement terminé avec succès!"
echo "📋 Images disponibles:"
echo "   - $FULL_IMAGE_NAME"
echo "   - $LATEST_IMAGE_NAME"

# 5. Instructions pour Render
echo ""
echo "🌐 Pour déployer sur Render:"
echo "   1. Connectez-vous à render.com"
echo "   2. Créez un nouveau Web Service"
echo "   3. Sélectionnez 'Deploy an existing image from a registry'"
echo "   4. Utilisez l'image: $LATEST_IMAGE_NAME"
echo "   5. Configurez le port: 80"
echo "   6. Ajoutez les variables d'environnement de base de données"
