<?php

require __DIR__.'/includes/core_class.php';
require __DIR__.'/includes/database_class.php';

/* path vars */
$APP_CONFIG_DIR = dirname(__DIR__) . '/config';
$TPL_DIR        = __DIR__ . '/config';
$SQL_PATH       = __DIR__ . '/assets/install.sql';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['hostname'] ?? '');
    $db_user = trim($_POST['username'] ?? '');
    $db_pass = (string)($_POST['password'] ?? '');
    $db_name = trim($_POST['database'] ?? '');

    $errors = [];
    if ($db_host === '' || $db_user === '' || $db_name === '') {
        $errors[] = 'Not all fields have been filled in correctly.';
    }

    try {
        if (!$errors) {
            // Create DB if it doesn't exist
            InstallerDB::createDatabaseIfNotExists([
                'hostname'=>$db_host,'username'=>$db_user,'password'=>$db_pass,'database'=>$db_name
            ]);

            $mysqli = InstallerDB::connect([
                'hostname'=>$db_host,'username'=>$db_user,'password'=>$db_pass,'database'=>$db_name
            ]);
        }

        [$baseUrl, $isHttps] = InstallerCore::buildBaseUrl();

        // Render templates
        $dbMap = ['%HOSTNAME%'=>$db_host,'%USERNAME%'=>$db_user,'%PASSWORD%'=>$db_pass,'%DATABASE%'=>$db_name];
        $dbRendered = InstallerCore::renderTemplateString($TPL_DIR.'/database.php', $dbMap);
        $left1 = array_filter(array_keys($dbMap), fn($ph)=>strpos($dbRendered,$ph)!==false);
        if ($left1) $errors[] = 'Template replacement failed for: '.implode(', ', $left1);

        $cfgMap = ['%BASE_URL%'=>$baseUrl,'%COOKIE_SECURE%'=>$isHttps ? 'TRUE' : 'FALSE'];
        $cfgRendered = InstallerCore::renderTemplateString($TPL_DIR.'/config.php', $cfgMap);
        $left2 = array_filter(array_keys($cfgMap), fn($ph)=>strpos($cfgRendered,$ph)!==false);
        if ($left2) $errors[] = 'Template replacement failed for: '.implode(', ', $left2);

        // Write/Rename
        if (!$errors) {
            if (!is_file($TPL_DIR.'/database.php')) throw new RuntimeException('Missing template: '.$TPL_DIR.'/database.php');
            if (!is_file($TPL_DIR.'/config.php'))   throw new RuntimeException('Missing template: '.$TPL_DIR.'/config.php');

            InstallerCore::testAtomicWrite($APP_CONFIG_DIR, 'database.php.tmp', 'ping');
            InstallerCore::testAtomicWrite($APP_CONFIG_DIR, 'config.php.tmp',   'ping');
        }

        // Import SQL if present
        if (!$errors && is_file($SQL_PATH)) {
            // normalize MyISAM-only options if needed
            $sql = file_get_contents($SQL_PATH);
            $sql = preg_replace('/\bROW_FORMAT\s*=\s*FIXED\b/i', 'ROW_FORMAT=DYNAMIC', $sql ?? '');
            InstallerDB::runSqlString($mysqli, $sql ?? '');
        }

        if (isset($mysqli) && $mysqli instanceof mysqli) $mysqli->close();

        // Commit writes + redirect
        if (!$errors) {
            InstallerCore::atomicWrite($APP_CONFIG_DIR.'/database.php', $dbRendered);
            InstallerCore::atomicWrite($APP_CONFIG_DIR.'/config.php',   $cfgRendered);

            $scheme = $isHttps ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base   = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME'] ?? ''), '', $_SERVER['SCRIPT_NAME'] ?? ''), '/').'/';
            header('Location: '.$scheme.'://'.$host.$base.'complete.php');
            exit;
        }
    } catch (Throwable $e) {
        $errors[] = 'Installer error: '.htmlentities($e->getMessage());
    }

    $message = '<p class="error">'.implode('<br>', $errors).'</p><br />';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Install | ForgeIgniter</title>
  <link rel="stylesheet" type="text/css" href="includes/install-foundation.css" />
  <link rel="stylesheet" type="text/css" href="includes/style.css" />
</head>
	<body>
						<!-- Create Super User ?
						<li class="li-style"><strong>Admin Setup</strong></li>
						-->
						<!-- System Configuration ?
						<li class="li-style"><strong>Setup Configuration</strong></li>
						-->
  <form id="install_form" method="post" action="<?=htmlspecialchars($_SERVER['PHP_SELF'] ?? 'dbsetup.php', ENT_QUOTES)?>">
    <h1>ForgeIgniter - MySQL Setup</h1>
    <hr class="hazar-separator">
    <div class="row">
      <div class="col-md-4 colstyle" style="height:320px">
        <div id="navside">
          <ul id="sidenav" class="nav nav-pills nav-stacked">
            <li class="active"><a href="#" style="background-color:rgb(50, 99, 50);"><strong>1. The Checks</strong></a></li>
            <li class="active"><a href="#"><strong>2. Database Setup</strong></a></li>
            <li class="li-style"><strong>3. Setup Complete</strong></li>
          </ul>
        </div>
      </div>
      <div class="col-md-8">
        <div id="right-content">
          <?= isset($message) ? $message : '' ?>
          <p><label for="hostname">Hostname</label><input type="text" name="hostname" id="hostname" value="localhost" required></p>
          <p><label for="database">Database Name</label><input type="text" name="database" id="database" required></p>
          <p><label for="username">Database Username</label><input type="text" name="username" id="username" required></p>
          <p><label for="password">Database Password</label><input type="password" name="password" id="password"></p>
        </div>
      </div>
    </div>
    <hr class="hazar-separator">
    <p class="p-container">
      <a href="http://www.forgeigniter.com/forums" target="_blank"><span>Need Help ?</span></a>
      <input type="submit" name="submit" id="submit" value="Next">
    </p>
  </form>
</body>
</html>
