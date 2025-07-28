-- Données de test pour l'application Woyofal

-- Insertion de clients de test
INSERT INTO clients (nom, prenom, telephone, adresse) VALUES 
('Diallo', 'Mamadou', '771234567', 'Dakar, Senegal'),
('Ndiaye', 'Fatou', '776543210', 'Thiès, Senegal'),
('Fall', 'Omar', '775555555', 'Saint-Louis, Senegal')
ON CONFLICT DO NOTHING;

-- Insertion de compteurs de test
INSERT INTO compteurs (numero, client_id, consommation_mensuelle, actif) VALUES 
('12345', 1, 120.50, true),
('67890', 2, 89.30, true),
('11111', 3, 200.75, true),
('22222', 1, 45.20, true)
ON CONFLICT DO NOTHING;

-- Insertion d'achats de test pour historique
INSERT INTO achats (reference, numero_compteur, code_recharge, montant, nombre_kwh, tranche, prix_kwh, date_achat, heure_achat) VALUES 
('WYF20240101120000001', '12345', '12345678901234567890', 5000, 54.95, 1, 91, '2024-01-01', '12:00:00'),
('WYF20240102130000002', '67890', '98765432109876543210', 3000, 32.97, 1, 91, '2024-01-02', '13:00:00'),
('WYF20240103140000003', '11111', '11111111111111111111', 8000, 78.43, 2, 102, '2024-01-03', '14:00:00')
ON CONFLICT DO NOTHING;
