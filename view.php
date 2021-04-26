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
?>

<!-- View -->
<html>
<head>
  <title>To Do List - Main Page</title>
  <?php //require_once "bootstrap.php"; ?>
</head>
<body style="font-family: sans-serif;">
<div class="container">
  <h1>To Do List for
  <?php
  if ( isset($_SESSION['email']) ) {
      echo htmlentities($_SESSION['email']);
      echo "</p>\n";
  }
  ?>
  </h1>

<!-- Search Tool -->
  <form method = POST>
    <p>Search with any key word in the list:
    <input type = "text" name = "word">
    <input type = "submit" name = "search" value = "Go">
    </p>
  </form>

    <?php
    // Searching for a row of values
    if (isset($_POST['search'])){
      $_SESSION['word'] = $_POST['word'];
      $stmt = $pdo->query("SELECT * FROM todo WHERE title OR description LIKE '%".$_SESSION['word']."%'");
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row != NULL){  // If it does, we draw the table
        echo('<table border="1">'."\n");
        echo "<tr><td>";
        echo("<center><b>Title</b></center>");
        echo("</td><td>");
        echo("<center><b>Description</center></b>");
        echo("</td><td>");
        echo("<center><b>Added Date</center></b>");
        echo("</td><td>");
        echo("<center><b>Due Date</center></b>");
        echo("</td><td>");
        echo("<center><b>Action</center></b>");
        $stmt = $pdo->query("SELECT * FROM todo WHERE title OR description LIKE '%".$_SESSION['word']."%'");
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo "<tr><td>";
            echo(htmlentities($row['title']));
            echo("</td><td>");
            echo(htmlentities($row['description']));
            echo("</td><td>");
            echo(htmlentities($row['added']));
            echo("</td><td>");
            echo(htmlentities($row['due']));
            echo("</td><td>");
            echo('<a href="edit.php?todo_id='.$row['todo_id'].'">Edit</a> | ');
            echo('<a href="done.php?todo_id='.$row['todo_id'].'">Done</a>');
            echo("</td></tr>\n");

        }
        echo('</table>'."\n");
      } else {
        $_SESSION['error'] = 'Record not found';
        header( 'Location: view.php' ) ;
        return;
      }
    }
    ?>

<!-- Sorting and Displaying Tool -->
  <form method = POST>
  <p><label for = "data"> How would you like this list to be sorted by?
    <select name = "column" id = "data">
      <option value = "added">-- Please Select --</option>
      <option value = "title">Title</option>
      <option value = "description">Description</option>
      <option value = "added">Added Date</option>
      <option value = "due">Due Date</option>
    </select>
    <input type = "submit" name = "sort" value = "Sort">
  </p>
  </form>

<!-- Displaying List of Data -->
<?php
// Check if we are logged in!
if (isset($_SESSION["email"])){ // If we are logged in
  // The following condition works in case the user decides to sort the list
  if(isset($_POST['sort'])){
    $_SESSION['column'] = $_POST['column'];
    $stmt = $pdo->query("SELECT * FROM todo ORDER BY ".$_SESSION['column']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row != NULL){  // If it does, we draw the table
      echo('<table border="1">'."\n");
      echo "<tr><td>";
      echo("<center><b>Title</b></center>");
      echo("</td><td>");
      echo("<center><b>Description</center></b>");
      echo("</td><td>");
      echo("<center><b>Added Date</center></b>");
      echo("</td><td>");
      echo("<center><b>Due Date</center></b>");
      echo("</td><td>");
      echo("<center><b>Action</center></b>");
      $stmt = $pdo->query("SELECT * FROM todo ORDER BY ".$_SESSION['column']);
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
          echo "<tr><td>";
          echo(htmlentities($row['title']));
          echo("</td><td>");
          echo(htmlentities($row['description']));
          echo("</td><td>");
          echo(htmlentities($row['added']));
          echo("</td><td>");
          echo(htmlentities($row['due']));
          echo("</td><td>");
          echo('<a href="edit.php?todo_id='.$row['todo_id'].'">Edit</a> | ');
          echo('<a href="done.php?todo_id='.$row['todo_id'].'">Done</a>');
          echo("</td></tr>\n");
      }
    }
  } else { // In case the user does not introduce any sort value, the table is displayed by default

  $stmt = $pdo->query("SELECT title, description, added, due, todo_id FROM todo");
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  // We first check if the database contains data
  if ($row != NULL){  // If it does, we draw the table
    echo('<table border="1">'."\n");
    echo "<tr><td>";
    echo("<center><b>Title</b></center>");
    echo("</td><td>");
    echo("<center><b>Description</center></b>");
    echo("</td><td>");
    echo("<center><b>Added Date</center></b>");
    echo("</td><td>");
    echo("<center><b>Due Date</center></b>");
    echo("</td><td>");
    echo("<center><b>Action</center></b>");
    $stmt = $pdo->query("SELECT title, description, added, due, todo_id FROM todo");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        echo "<tr><td>";
        echo(htmlentities($row['title']));
        echo("</td><td>");
        echo(htmlentities($row['description']));
        echo("</td><td>");
        echo(htmlentities($row['added']));
        echo("</td><td>");
        echo(htmlentities($row['due']));
        echo("</td><td>");
        echo('<a href="edit.php?todo_id='.$row['todo_id'].'">Edit</a> | ');
        echo('<a href="done.php?todo_id='.$row['todo_id'].'">Done</a>');
        echo("</td></tr>\n");
    }
  } else { // If the database doesn't contain data, we don't draw anything.
    echo "<strong>There are no registered To Do tasks. Start by adding a new task.</strong>";
  }
}
?>
  </table>
  <p><a href="add.php">Add New Task</a> | <a href = "completed.php">Check Completed Tasks</a></p>
  <p><a href = "export.php?todo">Export</a> your To Do Data List as a csv file.</a>
  <p><a href="logout.php">Logout</a></p>
<?php
} else  { // If we are not logged in
  die('Not logged in');
}
?>
</div>
</body>
</html>
