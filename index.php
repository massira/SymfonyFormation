<?php

use Config\Formation\Controllers\ConfigController;

require 'vendor/autoload.php';

$configController = new ConfigController();
$configController->sayHello('Mark');