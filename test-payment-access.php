<?php
/**
 * Script simple pour tester l'accès à la route admin/payments/{id}
 * Utile pour diagnostiquer les problèmes d'authentification
 */

echo "=== TEST ACCÈS ROUTE ADMIN ===\n\n";

// Simuler un appel cURL à la route
$url = 'https://lux-iles.embmission.com/admin/payments/23';

echo "URL testée : $url\n\n";

echo "Pour tester manuellement :\n";
echo "1. Ouvrez votre navigateur en mode navigation privée\n";
echo "2. Connectez-vous à l'admin\n";
echo "3. Ouvrez les outils de développement (F12)\n";
echo "4. Allez dans l'onglet Network\n";
echo "5. Cliquez sur l'icône œil d'un paiement\n";
echo "6. Vérifiez la réponse de la requête vers /admin/payments/{id}\n\n";

echo "Résultats attendus :\n";
echo "✅ Status: 200 OK\n";
echo "✅ Content-Type: application/json\n";
echo "✅ Response: {\"success\":true,\"payment\":{...},\"reservation\":{...}}\n\n";

echo "Si vous obtenez :\n";
echo "❌ Status: 302/401/403 → Problème d'authentification\n";
echo "❌ Status: 404 → Route non trouvée\n";
echo "❌ Status: 500 → Erreur serveur\n";
echo "❌ Content-Type: text/html → Redirection vers page de login\n\n";

echo "=== SOLUTIONS POSSIBLES ===\n\n";

echo "1. Vérifier la connexion admin :\n";
echo "   - Êtes-vous connecté ?\n";
echo "   - Votre session est-elle valide ?\n";
echo "   - Avez-vous les droits admin ?\n\n";

echo "2. Vérifier les cookies/session :\n";
echo "   - Clear les cookies du domaine\n";
echo "   - Reconnectez-vous\n\n";

echo "3. Vérifier la configuration serveur :\n";
echo "   - Les routes admin sont-elles chargées ?\n";
echo "   - Le middleware auth fonctionne-t-il ?\n\n";

echo "4. Test direct :\n";
echo "   - Essayez d'accéder directement à https://lux-iles.embmission.com/admin/dashboard\n";
echo "   - Si vous êtes redirigé vers login → problème d'authentification\n\n";