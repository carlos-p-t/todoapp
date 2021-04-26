<?php
session_start();
// In case the user tries to get into the site without logging in
if ( ! isset($_SESSION['email']) ) {
  die('Not logged in. Please <a href="login.php">Login</a><?php.');
}

require_once "pdo.php";

// Flash Messages
if ( isset($_SESSION["success"]) ) {
    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
    unset($_SESSION["success"]);
}

if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
    unset($_SESSION["error"]);
}

// Checking if the to be - deleted data comes from first or second database
if ( isset($_POST['delete']) && isset($_GET['todo_id']) ) {
    $sql = "DELETE FROM todo WHERE todo_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['todo_id']));
    $_SESSION['success'] = 'To Do record deleted';
    header( 'Location: view.php' ) ;
    return;
} else if ( isset($_POST['delete']) && isset($_GET['completed_id']) ) {
    $sql = "DELETE FROM completed WHERE completed_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['completed_id']));
    $_SESSION['success'] = 'Completed task record deleted';
    header( 'Location: completed.php' ) ;
    return;
}

// Checking if an id is present
if (isset($_GET['todo_id']) ) {
  $stmt = $pdo->prepare("SELECT title, todo_id FROM todo where todo_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['todo_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $confirmation = htmlentities($row['title']);
  if ( $row === false ) {
      $_SESSION['error'] = 'Bad value for todo_id';
      header( 'Location: view.php' ) ;
      return;
    }
} else if (isset($_GET['completed_id'])){
  $stmt = $pdo->prepare("SELECT title, completed_id FROM completed where completed_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['completed_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $confirmation = htmlentities($row['title']);
  if ( $row === false ) {
      $_SESSION['error'] = 'Bad value for completed_id';
      header( 'Location: completed.php' ) ;
      return;
    }
} else {
  $_SESSION['error'] = "Missing id";
  header('Location: view.php');
  return;
}

// If the user requested to cancel the operation we go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}
?>

<!-- View -->

<html>
<head>
  <title>Delete entry?</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body style="font-family: sans-serif;">
<div class="container">
<h1>Do you want to delete this entry?</h1>
<p>Confirm: Deleting <strong>"<?= $confirmation ?>"</strong></p>

<form method = "post">
<input type = "hidden" name = "todo_id" value = "<?= $row['todo_id'] ?>">
<input type = "hidden" name = "completed_id" value = "<?= $row['completed_id'] ?>">
<input type = "submit" value = "Delete" name = "delete">
<input type = "submit" value = "Cancel" name = "cancel">
</form>
</div>
</body>
</html>
