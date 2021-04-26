<?php
    session_start();
    // In case the user decides to cancel the login process
    if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }
    // For the password
    $salt = 'XyZzy12*_';
    $stored_hash = 'e4c3a437c6ba8b84824c28bda071e4f4';  // password is todo123

    // Checking if email and password were introduced
    if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
        unset($_SESSION["email"]);  // Logout current user
        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
          // In case of not introducing one of the fields
          $_SESSION["error"] = "User name and password are required";
          header("Location: login.php");
          return;
        } else if ((strpos($_POST['email'],'@') === false)){
          // Extra condition to check that User Name contains (@)
          $_SESSION["error"] = "Email must have an at-sign (@)";
          header("Location: login.php");
          return;
        } else { // In case the input data passes through the previous conditions
          // we can check if the password is correct
          $check = hash('md5', $salt.$_POST['pass']);
          if ( $check == $stored_hash ) { // If the password is correct
            // Redirect the browser to view.php
            $_SESSION["email"] = $_POST["email"];
            $_SESSION["success"] = "Logged in!";
            error_log("Login success ".$_SESSION["email"]);
            header( 'Location: view.php' );
            return;
        } else {
            $_SESSION["error"] = "Incorrect password.";
            error_log("Login fail ".$_SESSION["email"]." $check");
            header( 'Location: login.php' );
            return;
        }
      }
    }
?>

<!-- View -->
<html>
<head>
  <?php require_once "bootstrap.php"; ?>
  <title>Login Page</title>
</head>
<body style="font-family: sans-serif;" class="container">
<h1>Log In</h1>
<?php
    if ( isset($_SESSION["error"]) ) {
        echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
        unset($_SESSION["error"]);
    }
?>


<form method="POST">
<label for="nam">User Name</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</body>
