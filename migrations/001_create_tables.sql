-- Migration pour créer les tables de l'application Woyofal

-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) UNIQUE,
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des compteurs
CREATE TABLE IF NOT EXISTS compteurs (
    id SERIAL PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL,
    client_id INTEGER REFERENCES clients(id),
    consommation_mensuelle DECIMAL(10,2) DEFAULT 0.00,
    date_reset_tranche DATE DEFAULT DATE_TRUNC('month', CURRENT_DATE),
    actif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des achats
CREATE TABLE IF NOT EXISTS achats (
    id SERIAL PRIMARY KEY,
    reference VARCHAR(50) UNIQUE NOT NULL,
    numero_compteur VARCHAR(50) NOT NULL,
    code_recharge VARCHAR(20) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    nombre_kwh DECIMAL(10,2) NOT NULL,
    tranche INTEGER NOT NULL,
    prix_kwh DECIMAL(10,2) NOT NULL,
    date_achat DATE NOT NULL,
    heure_achat TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (numero_compteur) REFERENCES compteurs(numero)
);

-- Table de journalisation
CREATE TABLE IF NOT EXISTS journal (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    heure TIME NOT NULL,
    localisation VARCHAR(255),
    ip VARCHAR(45),
    statut VARCHAR(20) NOT NULL CHECK (statut IN ('Success', 'Échec')),
    numero_compteur VARCHAR(50),
    code_recharge VARCHAR(20),
    nombre_kwt DECIMAL(10,2),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_compteurs_numero ON compteurs(numero);
CREATE INDEX IF NOT EXISTS idx_achats_numero_compteur ON achats(numero_compteur);
CREATE INDEX IF NOT EXISTS idx_achats_reference ON achats(reference);
CREATE INDEX IF NOT EXISTS idx_journal_date ON journal(date);
CREATE INDEX IF NOT EXISTS idx_journal_statut ON journal(statut);

-- Fonction pour mettre à jour automatiquement updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers pour updated_at
CREATE TRIGGER update_clients_updated_at BEFORE UPDATE ON clients
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_compteurs_updated_at BEFORE UPDATE ON compteurs
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
