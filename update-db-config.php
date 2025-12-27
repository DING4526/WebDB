<?php
/**
 * Database configuration update helper script
 * Used by deploy.bat to safely update database configuration
 * 
 * Usage: php update-db-config.php <config_file> <host> <port> <dbname> <username> [password]
 */

if ($argc < 6) {
    echo "Usage: php update-db-config.php <config_file> <host> <port> <dbname> <username> [password]\n";
    exit(1);
}

$configFile = $argv[1];
$dbHost = $argv[2];
$dbPort = $argv[3];
$dbName = $argv[4];
$dbUser = $argv[5];
$dbPass = isset($argv[6]) ? $argv[6] : '';

if (!file_exists($configFile)) {
    echo "Error: Configuration file not found: $configFile\n";
    exit(1);
}

// Validate inputs to prevent malformed configuration
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $dbHost)) {
    echo "Error: Invalid host format. Only alphanumeric characters, dots, underscores, and hyphens are allowed.\n";
    exit(1);
}

if (!preg_match('/^[0-9]+$/', $dbPort) || intval($dbPort) < 1 || intval($dbPort) > 65535) {
    echo "Error: Invalid port number. Must be between 1 and 65535.\n";
    exit(1);
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
    echo "Error: Invalid database name. Only alphanumeric characters and underscores are allowed.\n";
    exit(1);
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbUser)) {
    echo "Error: Invalid username. Only alphanumeric characters and underscores are allowed.\n";
    exit(1);
}

$content = file_get_contents($configFile);

// Build new DSN (host, port, dbname are already validated)
$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";

// Escape values for PHP string (escape single quotes with backslash)
$escapedDsn = addcslashes($dsn, "'\\");
$escapedUser = addcslashes($dbUser, "'\\");
$escapedPass = addcslashes($dbPass, "'\\");

// Update DSN
$content = preg_replace(
    "/'dsn'\s*=>\s*'[^']*'/",
    "'dsn' => '$escapedDsn'",
    $content
);

// Update username
$content = preg_replace(
    "/'username'\s*=>\s*'[^']*'/",
    "'username' => '$escapedUser'",
    $content
);

// Update password
$content = preg_replace(
    "/'password'\s*=>\s*'[^']*'/",
    "'password' => '$escapedPass'",
    $content
);

if (file_put_contents($configFile, $content) === false) {
    echo "Error: Failed to write configuration file\n";
    exit(1);
}

echo "Database configuration updated successfully.\n";
echo "  DSN: $dsn\n";
echo "  Username: $dbUser\n";
exit(0);
