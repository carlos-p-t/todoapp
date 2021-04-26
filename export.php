<?php
session_start();
// In case the user tries to get into the site without logging in
if ( ! isset($_SESSION['email']) ) {
  die('Not logged in. Please <a href="login.php">Login</a><?php.');
}

require_once "pdo.php";

// For Flash Messages
if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
    unset($_SESSION["error"]);
}
if ( isset($_SESSION["success"]) ) {
    echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
    unset($_SESSION["success"]);
}

// For downloading - exporting
if (isset($_GET['todo'])){ // This is for exporting todo list
    $stmt = $pdo->query("SELECT todo_id, title, description, added, due FROM todo");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->execute();
    $filename = 'mytodolist.csv';
    $data = fopen($filename, 'w');
    $columnnames = ['todo_id', 'Title', 'Description', 'Added Date', 'Due Date'];
    fputcsv($data, $columnnames);
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      fputcsv($data, $row);
    }
    fclose($data);
    $_SESSION['success'] = 'File dowloaded, please check your main folder.';
    header( 'Location: view.php' ) ;
    return;
} else if (isset($_GET['completed'])){ // This is in case user wants to export completed task list
    $stmt = $pdo->query("SELECT completed_id, title, description, added, due FROM completed");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->execute();
    $filename = 'mycompletedtasklist.csv';
    $data = fopen($filename, 'w');
    $columnnames = ['completed_id', 'Title', 'Description', 'Added Date', 'Due Date'];
    fputcsv($data, $columnnames);
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        fputcsv($data, $row);
    }
    fclose($data);
    $_SESSION['success'] = 'File dowloaded, please check your main folder.';
    header( 'Location: completed.php' ) ;
    return;
} else {
    $_SESSION['error'] = 'Wrong id';
    header( 'Location: view.php' ) ;
    return;
}
?>
