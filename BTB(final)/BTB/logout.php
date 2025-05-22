<?php
session_start();
session_destroy();
header("Location: llogin.php");
exit();
