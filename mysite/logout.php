<?php
session_start();
session_destroy();
header('Location: /mysite/index.php');
exit;