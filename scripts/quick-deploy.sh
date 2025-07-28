#!/bin/bash

# Script de déploiement rapide pour brahim1205
set -e

echo "🚀 Déploiement rapide AppWoyofal pour brahim1205"
echo "=============================================="

# Variables
IMAGE_NAME="appwoyofal"
DOCKER_USERNAME="brahim1205"
VERSION="1.0.0"

# 1. Build
echo "📦 Construction de l'image..."
docker build -t $IMAGE_NAME:$VERSION .
docker build -t $IMAGE_NAME:latest .

# 2. Tag pour DockerHub
echo "🏷️  Tagging pour DockerHub..."
docker tag $IMAGE_NAME:$VERSION $DOCKER_USERNAME/$IMAGE_NAME:$VERSION
docker tag $IMAGE_NAME:latest $DOCKER_USERNAME/$IMAGE_NAME:latest

# 3. Push vers DockerHub
echo "⬆️  Push vers DockerHub..."
docker push $DOCKER_USERNAME/$IMAGE_NAME:$VERSION
docker push $DOCKER_USERNAME/$IMAGE_NAME:latest

echo "✅ Images déployées avec succès sur DockerHub !"
echo ""
echo "📋 Images disponibles :"
echo "   - $DOCKER_USERNAME/$IMAGE_NAME:$VERSION"
echo "   - $DOCKER_USERNAME/$IMAGE_NAME:latest"
echo ""
echo "🔗 Lien DockerHub : https://hub.docker.com/r/$DOCKER_USERNAME/$IMAGE_NAME"
echo ""
echo "🌐 Pour déployer sur Render, utilisez l'image :"
echo "   $DOCKER_USERNAME/$IMAGE_NAME:latest"
