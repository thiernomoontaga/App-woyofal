#!/bin/bash

echo "🧪 Test de l'API Woyofal"
echo "========================="

BASE_URL="http://localhost:8082"

echo ""
echo "1️⃣ Test de la page d'accueil:"
curl -s "$BASE_URL/" | jq . || curl -s "$BASE_URL/"

echo ""
echo "2️⃣ Test de l'endpoint santé via index.php:"
curl -s "$BASE_URL/index.php?/api/health" | jq . || curl -s "$BASE_URL/index.php?/api/health"

echo ""
echo "3️⃣ Test vérification compteur existant:"
curl -s "$BASE_URL/index.php?/api/verifier-compteur&numero=12345" | jq . || curl -s "$BASE_URL/index.php?/api/verifier-compteur&numero=12345"

echo ""
echo "4️⃣ Test vérification compteur inexistant:"
curl -s "$BASE_URL/index.php?/api/verifier-compteur&numero=99999" | jq . || curl -s "$BASE_URL/index.php?/api/verifier-compteur&numero=99999"

echo ""
echo "5️⃣ Test liste des compteurs:"
curl -s "$BASE_URL/index.php?/api/compteurs" | jq . || curl -s "$BASE_URL/index.php?/api/compteurs"

echo ""
echo "6️⃣ Test achat de crédit (succès):"
curl -s -X POST "$BASE_URL/index.php?/api/acheter" \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "67890", "montant": 1000}' | jq . || \
curl -s -X POST "$BASE_URL/index.php?/api/acheter" \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "67890", "montant": 1000}'

echo ""
echo "7️⃣ Test achat de crédit (compteur inexistant):"
curl -s -X POST "$BASE_URL/index.php?/api/acheter" \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "99999", "montant": 1000}' | jq . || \
curl -s -X POST "$BASE_URL/index.php?/api/acheter" \
  -H "Content-Type: application/json" \
  -d '{"numero_compteur": "99999", "montant": 1000}'

echo ""
echo "✅ Tests terminés !"
