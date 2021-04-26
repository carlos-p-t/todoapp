<?php
session_start();
if ( ! isset($_SESSION['email']) ) {
  die('Not logged in. Please <a href="login.php">Login</a><?php.');
}

require_once "pdo.php";

// Flash Messages
if ( isset($_SESSION["success"]) ) {
    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
    unset($_SESSION["success"]);
}

if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

// Data validation for editing
if ( isset($_POST['title']) || isset($_POST['description'])
     || isset($_POST['added']) || isset($_POST['due']) || isset($_POST['todo_id']) ) {
    if ((strlen($_POST['title']) < 1) ||
       (strlen($_POST['description']) < 1) ||
       (strlen($_POST['added']) < 1) ||
       (strlen($_POST['due']) < 1)) { // checking if all fields were introduced
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?todo_id=".$_POST['todo_id']);
        return;
    } else {
// Updating Entry
    $sql = "UPDATE todo SET title = :title, description = :description, added = :added,
            due = :due
            WHERE todo_id = :todo_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':added' => $_POST['added'],
        ':due' => $_POST['due'],
        ':todo_id' => $_POST['todo_id']));
    $_SESSION['success'] = 'Record edited';
    header( 'Location: view.php' ) ;
    return;
  }
}

// Checking if the correct id was entered
if ( ! isset($_GET['todo_id']) ) {
  $_SESSION['error'] = "Missing todo_id";
  header('Location: view.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM todo where todo_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['todo_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for todo_id';
    header( 'Location: view.php' ) ;
    return;
}

// For displaying the original content in the edit page
$title = htmlentities($row['title']);
$description = htmlentities($row['description']);
$added = htmlentities($row['added']);
$due = htmlentities($row['due']);
$todo_id = $row['todo_id'];
?>

<!-- View -->

<html>
<head>
  <title>Editing task details</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body style="font-family: sans-serif;">
<div class="container">

<h1>Please, edit the desired fields</h1>
<form method="POST">
<p>Title:
<input type="text" name="title" value="<?= $title ?>" size = "60"></p>
<p>Description:
<input type="text" name="description" value="<?= $description ?>" size = "60"></p>
<p>Added Date:
<input type="date" name="added" value="<?= $added ?>"></p>
<p>Due Date:
<input type="date" name="due" value="<?= $due ?>"></p>
<input type="hidden" name="todo_id" value="<?= $todo_id ?>">
<p><input type="submit" value="Save"/>
<a href="view.php">Cancel</a></p>
</form>
</div>
</body>
</html>
