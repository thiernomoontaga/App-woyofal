-- Données de test pour l'application Woyofal

-- Insertion des clients de test
INSERT INTO clients (nom, prenom, telephone, adresse) VALUES
('DIOP', 'Amadou', '771234567', 'Dakar, Plateau'),
('FALL', 'Fatou', '772345678', 'Thiès, Centre'),
('NDIAYE', 'Moussa', '773456789', 'Saint-Louis, Nord'),
('SARR', 'Aïssatou', '774567890', 'Kaolack, Centre'),
('BA', 'Ibrahima', '775678901', 'Ziguinchor, Sud');

-- Insertion des compteurs de test
INSERT INTO compteurs (numero, client_id, consommation_mensuelle, actif) VALUES
('COMP001234567890', 1, 120.50, TRUE),
('COMP001234567891', 2, 85.25, TRUE),
('COMP001234567892', 3, 200.75, TRUE),
('COMP001234567893', 4, 45.00, TRUE),
('COMP001234567894', 5, 350.25, TRUE),
('COMP001234567895', 1, 0.00, FALSE); -- Compteur inactif pour test

-- Insertion de quelques achats de test
INSERT INTO achats (reference, numero_compteur, code_recharge, montant, nombre_kwh, tranche, prix_kwh, date_achat, heure_achat) VALUES
('WYF202401011234567', 'COMP001234567890', '12345678901234567890', 5000.00, 54.95, 1, 91.00, CURRENT_DATE - INTERVAL '1 day', '10:30:00'),
('WYF202401021234568', 'COMP001234567891', '12345678901234567891', 10000.00, 109.89, 1, 91.00, CURRENT_DATE - INTERVAL '2 days', '14:15:00'),
('WYF202401031234569', 'COMP001234567892', '12345678901234567892', 15000.00, 147.06, 2, 102.00, CURRENT_DATE - INTERVAL '3 days', '16:45:00');

-- Insertion de logs de test
INSERT INTO journal (date, heure, localisation, ip, statut, numero_compteur, code_recharge, nombre_kwt, message) VALUES
(CURRENT_DATE - INTERVAL '1 day', '10:30:00', '127.0.0.1', '127.0.0.1', 'Success', 'COMP001234567890', '12345678901234567890', 54.95, 'Achat effectué avec succès'),
(CURRENT_DATE - INTERVAL '2 days', '14:15:00', '127.0.0.1', '127.0.0.1', 'Success', 'COMP001234567891', '12345678901234567891', 109.89, 'Achat effectué avec succès'),
(CURRENT_DATE - INTERVAL '3 days', '16:45:00', '127.0.0.1', '127.0.0.1', 'Success', 'COMP001234567892', '12345678901234567892', 147.06, 'Achat effectué avec succès'),
(CURRENT_DATE, '09:00:00', '127.0.0.1', '127.0.0.1', 'Échec', 'COMP999999999999', NULL, NULL, 'Compteur non trouvé');
