<?php
require 'config/init.php';
session_destroy();
header("Location: auth.php?action=login");
exit;
