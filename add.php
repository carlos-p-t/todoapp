<?php
session_start();
// In case the user tries to get into the site without logging in
if ( ! isset($_SESSION['email']) ) {
  die('Not logged in. Please <a href="login.php">Login</a><?php.');
}

require_once "pdo.php";
// If the user requested to cancel the operation we go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

// For Flash Messages
if ( isset($_SESSION["success"]) ) {
    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
    unset($_SESSION["success"]);
}
if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
    unset($_SESSION["error"]);
}

// Checking input values
if (isset($_POST['title']) || isset($_POST['description']) || isset($_POST['added']) || isset($_POST['due'])) {
    if ((strlen($_POST['title']) < 1) ||
        (strlen($_POST['description']) < 1) ||
        (strlen($_POST['added']) < 1) ||
        (strlen($_POST['due']) < 1)) { // checking if all fields were introduced
        $_SESSION["error"] = "All fields are required";
        header( 'Location: add.php' ) ;
        return;
    } else { // If all the previous conditions are met, the values can be inserted into the database
      if ( isset($_POST['title']) && isset($_POST['description'])
           && isset($_POST['added']) && isset($_POST['due'])) {
          $_SESSION['title'] = htmlentities($_POST['title']);
          $_SESSION['description'] = htmlentities($_POST['description']);
          $_SESSION['added'] = htmlentities($_POST['added']);
          $_SESSION['due'] = htmlentities($_POST['due']);
          $sql = "INSERT INTO todo (title, description, added, due)
                    VALUES (:title, :description, :added, :due)";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
              ':title' => $_SESSION['title'],
              ':description' => $_SESSION['description'],
              ':added' => $_SESSION['added'],
              ':due' => $_SESSION['due']));
      }
      $_SESSION["success"] = "New task successfully added";
      header("Location: view.php");
      return;
    }
  }
?>

<!-- View -->
<html>
<head>
  <title>Adding a New Task</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body style="font-family: sans-serif;">
<div class="container">
  <h1>Adding a task for
  <?php
  if ( isset($_SESSION['email']) ) {
      echo htmlentities($_SESSION['email']);
      echo "</p>\n";
  }
  ?>
  </h1>

<form method="POST">
<p>Title:
<input type="text" name="title" size="60"></p>
<p>Description:
<input type="text" name="description" size="60"></p>
<p>Added Date:
<input type="date" name="added"></p>
<p>Due Date:
<input type="date" name="due"></p>
<p><input type="submit" value="Add"/>
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
</body>
</html>
