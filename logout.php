<?php
    session_start();
    session_destroy(); // This to erase the session
    header("Location: index.php");
