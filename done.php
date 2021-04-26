<?php
require_once "pdo.php";
session_start();

// If user selects "Completed", the app copies the data from the first database to the second one, deleting
// the information from the fisrt one.

if ( isset($_POST['completed']) && isset($_POST['todo_id']) ) {
    $stmt = $pdo->prepare("SELECT title, description, added, due FROM todo where todo_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['todo_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $completed_title = $row['title'];
    $completed_description = $row['description'];
    $completed_added = $row['added'];
    $completed_due = $row['due'];
    $sql = "INSERT INTO completed (title, description, added, due)
              VALUES (:completed_title, :completed_description, :completed_added, :completed_due)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':completed_title' => $completed_title,
        ':completed_description' => $completed_description,
        ':completed_added' => $completed_added,
        ':completed_due' => $completed_due));
// Deleting the data from the first database
    $sql = "DELETE FROM todo WHERE todo_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['todo_id']));

    $_SESSION['success'] = "Task Completed";
    header( 'Location: completed.php' ) ;
    return;
}

// If user selects Delete, it redirects to delete.php
if ( isset($_POST['delete']) && isset($_POST['todo_id']) ) {
    $stmt = $pdo->prepare("SELECT title, todo_id FROM todo where todo_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['todo_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    header( 'Location: delete.php?todo_id='.$row['todo_id'] ) ;
    return;
}

// If the user requested to cancel the operation we go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

// Making sure the correct id is present
if ( ! isset($_GET['todo_id']) ) {
  $_SESSION['error'] = "Missing todo_id";
  header('Location: view.php');
  return;
}

$stmt = $pdo->prepare("SELECT title, todo_id FROM todo where todo_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['todo_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for todo_id';
    header( 'Location: view.php' ) ;
    return;
}

?>


<!-- View -->

<html>
<head>
  <title>Is your task done?</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body style="font-family: sans-serif;">
<div class="container">
<h1> Completed task? </h1>
<p>If task <strong>"<?= htmlentities($row['title']) ?>"</strong> is completed, please choose next action. </p>
<p><strong>Note:</strong> You can select one of the following operations.
  <ul>
    <li> By pressing "Completed", the task will be stored in your Completed Tasks list.</li>
    <li> By pressing "Delete", this task will be permanently deleted.</li>
    <li> By pressing "Cancel", the operation will be canceled.</li>
  </ul>
<form method = "POST">
<input type = "hidden" name = "todo_id" value = "<?= $row['todo_id'] ?>">
<input type = "submit" value = "Completed" name = "completed">
<input type = "submit" value = "Delete" name = "delete">
<input type = "submit" value = "Cancel" name = "cancel">
</form>
</div>
</body>
</html>
