<?php
if (!function_exists('mysqli_init') && !extension_loaded('mysqli')) {
    echo 'MySQLi extension is missing.';
} else {
    echo 'MySQLi extension is enabled.';
}
?>