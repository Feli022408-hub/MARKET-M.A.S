<?php

if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
 
    require_once("libraries/password_compatibility_library.php");
}


require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
   
   header("location: stock.php");

} else {
    ?>
	<!DOCTYPE html>
<html lang="es">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
  <title>Market M.A.S | Login</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
	<style>
		.profile-img-card {
    width: 250px; 
    height: 250px; 
    object-fit: cover; 
    border-radius: 50%; 
}
		</style>
</head>
<body>
 <div class="container">
        <div class="card card-container">
			<center>
            <img id="profile-img" class="profile-img-card" src="img/avatar.png" width="250px" height="250px" alt="Profile Image">
			</center>

            <p id="profile-name" class="profile-name-card"></p>
            <form method="post" accept-charset="utf-8" action="login.php" name="loginform" autocomplete="off" role="form" class="form-signin">
			<?php
				
				if (isset($login)) {
					if ($login->errors) {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
						    <strong>Error!</strong>

						<?php
						foreach ($login->errors as $error) {
							echo $error;
						}
						?>
						</div>
						<?php
					}
					if ($login->messages) {
						?>
						<div class="alert alert-success alert-dismissible" role="alert">
						    <strong>Aviso!</strong>
						<?php
						foreach ($login->messages as $message) {
							echo $message;
						}
						?>
						</div>
						<?php
					}
				}
				?>
                <span id="reauth-email" class="reauth-email"></span>
                <input class="form-control" placeholder="Usuario" name="user_name" type="text" value="" autofocus="" required>
                <input class="form-control" placeholder="Contraseña" name="user_password" type="password" value="" autocomplete="off" required>
                <button type="submit" class="btn btn-lg btn-success btn-block btn-signin" name="login" id="submit">Iniciar Sesión</button>
            </form><!-- /form -->

        </div><!-- /card-container -->
    </div><!-- /container -->
  </body>
</html>

	<?php
}
