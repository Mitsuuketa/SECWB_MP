<?php

$debug = getenv('DEBUG') === 'true';

function handle_error($exception) {
    global $debug;
    if ($debug) {
        return $exception->getMessage() . "\n" . $exception->getTraceAsString();
    } else {
        return "An error occurred. Please contact support.";
    }
}

set_exception_handler('handle_error');
