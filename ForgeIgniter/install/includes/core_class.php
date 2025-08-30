<?php
declare(strict_types=1);

class InstallerCore
{
    /**
     * ATOMIC INSTALLER - 0.1
     * Write to a temp file in the same dir, fsync, then rename.
     * Idea from Linux, atomic ;)
     */
    public static function atomicWrite(string $path, string $contents, int $mode = 0644): void
    {
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new RuntimeException("Make DIR failed: {$dir}");
        }

        $tmp = tempnam($dir, 'cfg_');
        if ($tmp === false) {
            throw new RuntimeException('Temp DIR failed');
        }

        $fh = fopen($tmp, 'wb');
        if ($fh === false) {
            @unlink($tmp);
            throw new RuntimeException("Can not open: {$tmp}");
        }

        if (fwrite($fh, $contents) === false) {
            fclose($fh);
            @unlink($tmp);
            throw new RuntimeException("Failed: {$tmp}");
        }

        fflush($fh);
        if (function_exists('fsync')) {
            @fsync($fh);
        }
        fclose($fh);
        @chmod($tmp, $mode);

        // If target exists, delete first to avoid rename() failure
        if (file_exists($path)) {
            @unlink($path);
        }

        if (!@rename($tmp, $path)) {
            @unlink($tmp);
            throw new RuntimeException("failed to rename: {$path}");
        }
    }

    // Read a template file and apply placeholder
    public static function renderTemplateString(string $tplPath, array $map): string
    {
        $tpl = file_get_contents($tplPath);
        if ($tpl === false) {
            throw new RuntimeException("Failed to read file: {$tplPath}");
        }
        return strtr($tpl, $map);
    }


    // Check we can write and rename into a DIR, clean up the rest.
    public static function testAtomicWrite(string $dir, string $filename, string $contents): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new RuntimeException("mkdir failed: {$dir}");
        }

        $tmp = tempnam($dir, 'cfg_');
        if ($tmp === false) {
            throw new RuntimeException("temp failed in {$dir}");
        }

        $fh = fopen($tmp, 'wb');
        if ($fh === false) {
            @unlink($tmp);
            throw new RuntimeException("fopen failed: {$tmp}");
        }

        if (fwrite($fh, $contents) === false) {
            fclose($fh);
            @unlink($tmp);
            throw new RuntimeException("fwrite failed: {$tmp}");
        }

        fflush($fh);
        if (function_exists('fsync')) {
            @fsync($fh);
        }
        fclose($fh);

        $target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($target)) {
            @unlink($target);
        }

        if (!@rename($tmp, $target)) {
            @unlink($tmp);
            throw new RuntimeException("Rename failed into {$target}");
        }

        @unlink($target);
    }


    // Replace placeholders

    public static function writeTemplate(string $templatePath, string $targetPath, array $map): void
    {
        $out = self::renderTemplateString($templatePath, $map);
        self::atomicWrite($targetPath, $out);
    }

    // Set Placeholders
    public static function writeDatabasePhp(array $vars, string $templatePath, string $targetPath): void
    {
        $map = [
            '%HOSTNAME%' => (string)($vars['host'] ?? ''),
            '%USERNAME%' => (string)($vars['user'] ?? ''),
            '%PASSWORD%' => (string)($vars['pass'] ?? ''),
            '%DATABASE%' => (string)($vars['name'] ?? ''),
        ];
        self::writeTemplate($templatePath, $targetPath, $map);
    }

    /**
     * Set a base with yummy cookie  ?
     * Tests was okay, may add later
     */
    public static function writeConfigPhp(array $vars, string $templatePath, string $targetPath): void
    {
        $cookieSecure = !empty($vars['cookie_secure']) && $vars['cookie_secure'] !== '0';
        $map = [
            '%BASE_URL%'      => (string)($vars['base_url'] ?? ''),
            '%COOKIE_SECURE%' => $cookieSecure ? 'TRUE' : 'FALSE',
        ];
        self::writeTemplate($templatePath, $targetPath, $map);
    }

    /**
     * Build a base ?
     * Tests was okay, may add later, remember, don't just play with local svr
     */
    public static function buildBaseUrl(array $trustedProxies = ['127.0.0.1','::1']): array
    {
        $remote    = $_SERVER['REMOTE_ADDR'] ?? '';
        $fromProxy = in_array($remote, $trustedProxies, true);

        $isHttps =
            (isset($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) === 'on') ||
            (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
            ($fromProxy && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
            ($fromProxy && isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on');

        $scheme = $isHttps ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $path   = ($dir === '' || $dir === '/') ? '/' : $dir . '/';

        return [$scheme . '://' . $host . $path, $isHttps];
    }
}
