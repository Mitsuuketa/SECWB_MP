<?php
require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create the logger
$logger = new Logger('app_logger');

// Now add some handlers
$logger->pushHandler(new StreamHandler(__DIR__.'/app.log', Logger::DEBUG));

// Logging functions
function log_authentication($username, $success) {
    global $logger;
    if ($success) {
        $logger->info("Authentication successful for user: $username");
    } else {
        $logger->warning("Authentication failed for user: $username");
    }
}

function log_transaction($transaction_id, $status) {
    global $logger;
    $logger->info("Transaction $transaction_id status: $status");
}

function log_admin_action($action, $admin_user) {
    global $logger;
    $logger->info("Admin action performed by $admin_user: $action");
}
