<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système DDMI - CREG Center</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Système DDMI</h1>
            <p>Diagnostic de la Dépendance et de la Migration Internationale</p>
        </header>
        
        <main>
            <section class="card">
                <h2>Bienvenue sur le système DDMI</h2>
                <p>Cette plateforme permet de calculer et analyser les indicateurs DDMI à partir des données des indicateurs élémentaires.</p>
                
                <div class="actions">
                    <a href="login" class="btn btn-primary">Se connecter</a>
                    <a href="about" class="btn btn-secondary">En savoir plus</a>
                </div>
            </section>
            
            <section class="features">
                <div class="feature-card">
                    <h3>Paramétrage</h3>
                    <p>Configuration des pays, années et indicateurs</p>
                </div>
                
                <div class="feature-card">
                    <h3>Calcul</h3>
                    <p>Normalisation et agrégation des indicateurs</p>
                </div>
                
                <div class="feature-card">
                    <h3>Visualisation</h3>
                    <p>Tableaux de bord et rapports analytiques</p>
                </div>
            </section>
        </main>
        
        <footer>
            <p>&copy; <?= date('Y') ?> CREG Center. Tous droits réservés.</p>
        </footer>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>