<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système DDMI</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .login-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-header {
            background-color: #2c6da8;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 500;
        }
        
        .login-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2c6da8;
            outline: none;
            box-shadow: 0 0 0 2px rgba(44, 109, 168, 0.2);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .btn {
            display: inline-block;
            background-color: #2c6da8;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            text-align: center;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: #1c4f7c;
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .login-footer {
            text-align: center;
            padding: 15px 20px;
            border-top: 1px solid #eee;
        }
        
        .login-footer a {
            color: #2c6da8;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="DDMI Logo" onerror="this.src='../assets/img/logo-placeholder.png'">
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <h1>Connexion au Système DDMI</h1>
            </div>
            
            <div class="login-body">
                <?php if (!empty($flashMessages)): ?>
                    <?php foreach ($flashMessages as $type => $messages): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="alert alert-<?= $type ?>">
                                <?= $message ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Correction: Utiliser un chemin absolu pour action -->
                <form action="/ddmi/src/public/authenticate" method="post">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Se connecter</button>
                    </div>
                </form>
            </div>
            
            <div class="login-footer">
                <a href="#">Mot de passe oublié?</a>
            </div>
        </div>
    </div>
</body>
</html>