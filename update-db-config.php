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

$content = file_get_contents($configFile);

// Build new DSN
$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";

// Update DSN
$content = preg_replace(
    "/'dsn'\s*=>\s*'[^']*'/",
    "'dsn' => '$dsn'",
    $content
);

// Update username
$content = preg_replace(
    "/'username'\s*=>\s*'[^']*'/",
    "'username' => '$dbUser'",
    $content
);

// Update password
$content = preg_replace(
    "/'password'\s*=>\s*'[^']*'/",
    "'password' => '$dbPass'",
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
