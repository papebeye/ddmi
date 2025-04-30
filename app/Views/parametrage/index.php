<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramétrage - Système DDMI</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c6da8;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .user-name {
            margin-right: 15px;
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        
        .nav-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
            padding-left: 0;
            list-style: none;
        }
        
        .nav-tabs li {
            margin-bottom: -1px;
        }
        
        .nav-tabs a {
            display: block;
            padding: 10px 15px;
            border: 1px solid transparent;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            text-decoration: none;
            color: #495057;
            margin-right: 5px;
        }
        
        .nav-tabs a.active {
            color: #2c6da8;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        
        .nav-tabs a:hover:not(.active) {
            border-color: #e9ecef #e9ecef #dee2e6;
            background-color: #f8f9fa;
        }
        
        .tab-content {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
            padding: 20px;
        }
        
        .card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        .btn {
            display: inline-block;
            background-color: #2c6da8;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Module de Paramétrage DDMI</h1>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                <a href="<?= $this->baseUrl ?>/logout" class="logout-btn">Déconnexion</a>
            </div>
        </header>
        
        <ul class="nav-tabs">
            <li><a href="#pays" class="active" onclick="openTab(event, 'pays')">Pays</a></li>
            <li><a href="#annees" onclick="openTab(event, 'annees')">Années de travail</a></li>
            <?php if ($this->auth->isAdmin() || $this->auth->isCentralRole()): ?>
                <li><a href="#utilisateurs" onclick="openTab(event, 'utilisateurs')">Utilisateurs</a></li>
            <?php endif; ?>
            <li><a href="#indicateurs" onclick="openTab(event, 'indicateurs')">Indicateurs</a></li>
        </ul>
        
        <div id="pays" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span>Liste des pays</span>
                    <?php if ($this->auth->isAdmin() || $this->auth->isCentralRole()): ?>
                        <a href="<?= $this->baseUrl ?>/parametrage/pays/ajouter" class="btn btn-sm">Ajouter un pays</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 10%">Code ISO</th>
                                <th style="width: 60%">Nom</th>
                                <th style="width: 10%">Statut</th>
                                <th style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pays)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">Aucun pays trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pays as $p): ?>
                                    <tr>
                                        <td><?= $p['id'] ?></td>
                                        <td><?= htmlspecialchars($p['code_iso']) ?></td>
                                        <td><?= htmlspecialchars($p['nom']) ?></td>
                                        <td>
                                            <?php if ($p['actif']): ?>
                                                <span class="badge badge-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?= $this->baseUrl ?>/parametrage/pays/editer/<?= $p['id'] ?>" class="btn btn-sm btn-warning">Éditer</a>
                                                <?php if ($this->auth->isAdmin()): ?>
                                                    <?php if ($p['actif']): ?>
                                                        <a href="<?= $this->baseUrl ?>/parametrage/pays/desactiver/<?= $p['id'] ?>" class="btn btn-sm btn-danger">Désactiver</a>
                                                    <?php else: ?>
                                                        <a href="<?= $this->baseUrl ?>/parametrage/pays/activer/<?= $p['id'] ?>" class="btn btn-sm btn-success">Activer</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div id="annees" class="tab-content" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <span>Années de travail</span>
                    <?php if ($this->auth->isAdmin() || $this->auth->isCentralRole()): ?>
                        <a href="<?= $this->baseUrl ?>/parametrage/annees/ajouter" class="btn btn-sm">Ajouter une année</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">Année</th>
                                <th style="width: 25%">Pays</th>
                                <th style="width: 20%">Statut</th>
                                <th style="width: 15%">Date de création</th>
                                <th style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($annees)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Aucune année de travail trouvée</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Fonctionnalité disponible prochainement</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if ($this->auth->isAdmin() || $this->auth->isCentralRole()): ?>
            <div id="utilisateurs" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <span>Gestion des utilisateurs</span>
                        <a href="<?= $this->baseUrl ?>/parametrage/utilisateurs/ajouter" class="btn btn-sm">Ajouter un utilisateur</a>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">Nom</th>
                                    <th style="width: 15%">Nom d'utilisateur</th>
                                    <th style="width: 15%">Pays</th>
                                    <th style="width: 10%">Rôle</th>
                                    <th style="width: 10%">Statut</th>
                                    <th style="width: 25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($utilisateurs)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Aucun utilisateur trouvé</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($utilisateurs as $u): ?>
                                        <tr>
                                            <td><?= $u['id'] ?></td>
                                            <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                                            <td><?= htmlspecialchars($u['username']) ?></td>
                                            <td><?= htmlspecialchars($u['pays_nom'] ?: '-') ?></td>
                                            <td>
                                                <?php if ($u['role'] === 'central'): ?>
                                                    <span class="badge badge-warning">Central</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Pays</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['actif']): ?>
                                                    <span class="badge badge-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= $this->baseUrl ?>/parametrage/utilisateurs/editer/<?= $u['id'] ?>" class="btn btn-sm btn-warning">Éditer</a>
                                                    <?php if ($u['actif']): ?>
                                                        <a href="<?= $this->baseUrl ?>/parametrage/utilisateurs/desactiver/<?= $u['id'] ?>" class="btn btn-sm btn-danger">Désactiver</a>
                                                    <?php else: ?>
                                                        <a href="<?= $this->baseUrl ?>/parametrage/utilisateurs/activer/<?= $u['id'] ?>" class="btn btn-sm btn-success">Activer</a>
                                                    <?php endif; ?>
                                                    <a href="<?= $this->baseUrl ?>/parametrage/utilisateurs/reinitialiser/<?= $u['id'] ?>" class="btn btn-sm">Réinitialiser MDP</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div id="indicateurs" class="tab-content" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <span>Paramétrage des indicateurs</span>
                </div>
                <div class="card-body">
                    <p style="text-align: center;">Cette section permettra de paramétrer les indicateurs, les dimensions et les formules de calcul.</p>
                    <p style="text-align: center;">Fonctionnalité disponible prochainement.</p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Système DDMI - Centre de Recherche en Économie et Gestion (CREG) &copy; <?= date('Y') ?></p>
        </div>
    </div>
    
    <script>
        function openTab(evt, tabName) {
            // Masquer tous les contenus d'onglets
            var tabContents = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].style.display = "none";
            }
            
            // Désactiver tous les onglets
            var tabLinks = document.getElementsByClassName("nav-tabs")[0].getElementsByTagName("a");
            for (var i = 0; i < tabLinks.length; i++) {
                tabLinks[i].className = tabLinks[i].className.replace(" active", "");
            }
            
            // Afficher le contenu de l'onglet sélectionné
            document.getElementById(tabName).style.display = "block";
            
            // Activer l'onglet sélectionné
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>