<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pages/login.css">
    <link rel="stylesheet" href="css/index.css">
    <script src="js/W3IncludeHTML.js"></script>
    <title>Register</title>
</head>
<body>
    <header>
    <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php">A propos</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="index.php">Quiz</a></li>
                    <li><a href="index.php">Résultats</a></li>
                    <li><a href="index.php">Déconnexion</a></li>
                <?php else: ?>
                    <li id="loginButton"><a href="login.php">Login</a></li>
                    <li id="registerButton"><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Register</h1>
        <form action="">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd">
            <label for="confirm-pwd">Confirm Password</label>
            <input type="password" name="confirm-pwd" id="confirm-pwd">
            <input id="submit" type="submit" value="Register">
        </form>
    </main> 
</body>
</html>