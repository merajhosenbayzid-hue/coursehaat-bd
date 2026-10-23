<?php
require __DIR__ . '/api.php';
do_logout();
header('Location: index.php');
exit;
