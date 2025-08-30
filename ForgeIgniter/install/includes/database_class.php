<?php
declare(strict_types=1);

class InstallerDB
{

    // Connect to DB and set utf8mb4.
    public static function connect(array $cfg): mysqli
    {
        $host   = $cfg['hostname'] ?? 'localhost';
        $user   = $cfg['username'] ?? '';
        $pass   = $cfg['password'] ?? '';
        $db     = $cfg['database'] ?? '';
        $port   = (int)($cfg['port'] ?? 3306);
        $socket = $cfg['socket'] ?? null;

        $mysqli = @mysqli_init();
        if (!$mysqli) {
            throw new RuntimeException('mysqli_init failed');
        }

        @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

        if (!@mysqli_real_connect($mysqli, $host, $user, $pass, $db, $port, $socket)) {
            $err = mysqli_connect_error();
            throw new RuntimeException('DB connection failed: '.$err);
        }

        if (!$mysqli->set_charset('utf8mb4')) {
            throw new RuntimeException('Failed to set charset: '.$mysqli->error);
        }

        return $mysqli;
    }

    public static function createDatabaseIfNotExists(array $cfg, string $charset = 'utf8mb4', string $collation = 'utf8mb4_unicode_ci'): void
    {
        $host   = $cfg['hostname'] ?? 'localhost';
        $user   = $cfg['username'] ?? '';
        $pass   = $cfg['password'] ?? '';
        $name   = $cfg['database'] ?? '';
        $port   = (int)($cfg['port'] ?? 3306);
        $socket = $cfg['socket'] ?? null;

        if ($name === '') {
            throw new InvalidArgumentException('Database name is required');
        }

        $mysqli = @mysqli_init();
        if (!$mysqli) {
            throw new RuntimeException('mysqli_init failed');
        }
        @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

        if (!@mysqli_real_connect($mysqli, $host, $user, $pass, null, $port, $socket)) {
            $err = mysqli_connect_error();
            throw new RuntimeException('Server connection failed: '.$err);
        }

        $charset   = preg_replace('/[^a-z0-9_]/i', '', $charset) ?: 'utf8mb4';
        $collation = preg_replace('/[^a-z0-9_]/i', '', $collation) ?: 'utf8mb4_unicode_ci';

        $sql = sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s',
            str_replace('`','``',$name),
            $charset,
            $collation
        );

        if (!$mysqli->query($sql)) {
            $e = $mysqli->error;
            $mysqli->close();
            throw new RuntimeException('CREATE DATABASE failed: '.$e);
        }

        $mysqli->close();
    }

    public static function runSqlString(mysqli $db, string $sql): void
    {
        if ($sql === '' || trim($sql) === '') return;

        // Strip UTF-8 BOM, normalise newlines
        if (substr($sql, 0, 3) === "\xEF\xBB\xBF") $sql = substr($sql, 3);
        $sql = str_replace("\r\n", "\n", $sql);

        if (!$db->multi_query($sql)) {
            throw new RuntimeException('SQL error: '.$db->error);
        }

        do {
            if ($res = $db->store_result()) {
                $res->free();
            }
        } while ($db->more_results() && $db->next_result());

        if ($db->errno) {
            throw new RuntimeException('SQL error: '.$db->error);
        }
    }

    public static function runSqlFile(mysqli $db, string $path): void
    {
        if (!is_file($path)) {
            throw new RuntimeException("SQL file not found: {$path}");
        }
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("Cannot read SQL file: {$path}");
        }
        self::runSqlString($db, $sql);
    }
}
