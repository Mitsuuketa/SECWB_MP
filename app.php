<?php
require 'logger.php';
require 'error_handling.php';

function authenticate_user($username, $password) {
    try {
        // Dummy authentication logic
        if ($username === 'admin' && $password === 'secret') {
            log_authentication($username, true);
            return true;
        } else {
            log_authentication($username, false);
            return false;
        }
    } catch (Exception $e) {
        echo handle_error($e);
    }
}

function perform_transaction($transaction_id) {
    try {
        log_transaction($transaction_id, 'started');
        // Transaction processing...
        // Intentionally raise an exception to test error handling
        throw new Exception("Simulated transaction error");
        log_transaction($transaction_id, 'completed');
    } catch (Exception $e) {
        echo handle_error($e);
    }
}

function admin_action($action, $admin_user) {
    try {
        log_admin_action($action, $admin_user);
    } catch (Exception $e) {
        echo handle_error($e);
    }
}

// Example usage
authenticate_user('admin', 'secret');
authenticate_user('user', 'wrongpassword');
perform_transaction(12);
admin_action('delete_user', 'admin');
