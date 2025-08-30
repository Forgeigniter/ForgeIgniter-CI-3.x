<?php

/*  NOTES:
 | --------
 | Drop bootstrap
 |
 | Create Super User (main admin) needs to be created.
 |
 | Main System Configuration, like dir paths etc need to be created.
 |
 | FILE: ForgeIgniter/install/index.php
 | VERSION: 0.3
*/

// forgeigniter/install/index.php

// Shhh, it will all be guuuud lol
error_reporting(0);

// Paths
$APP_CONFIG_DIR = dirname(__DIR__).'/../config';

// The Checks, we should really put something here, interesting example ?
$checks = [
    'php'        => version_compare(PHP_VERSION, '8.1.0', '>='),
    'mysqli'     => extension_loaded('mysqli'),
    'mbstring'   => extension_loaded('mbstring'),
    'intl'       => extension_loaded('intl'),
    'json'       => extension_loaded('json'),
    'openssl'    => extension_loaded('openssl'),
];

// Messages
$msg = [
    'php_ok'        => 'Great - PHP 8.1+ detected ('.PHP_VERSION.').', // 7.4 ok though
    'php_bad'       => 'Please upgrade to PHP 8.1+ (current: '.PHP_VERSION.').',
    'ext_ok'        => 'Loaded.',
    'ext_bad'       => 'Missing.',
    'cfg_ok'        => 'Config directory is writable.',
];

$allGood = $checks['php']
    && $checks['mysqli']
    && $checks['mbstring']
    && $checks['intl']
    && $checks['json']
    && $checks['openssl']
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Install | ForgeIgniter</title>
<link rel="stylesheet" type="text/css" href="includes/install-foundation.css" />
<link rel="stylesheet" type="text/css" href="includes/style.css" />
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>

<form id="install_form">
  <h1>ForgeIgniter - System Checks</h1>
  <hr class="hazar-separator">

  <div class="row">
    <div class="col-md-4 colstyle" style="height:320px">
      <div id="navside">
        <ul id="sidenav" class="nav nav-pills nav-stacked">
          <li class="active"><a href="#"><strong>1. The Checks</strong></a></li>
          <li class="li-style"><strong>2. Database Setup</strong></li>
          <li class="li-style"><strong>3. Setup Complete</strong></li>
        </ul>
      </div>
    </div>

    <div class="col-md-8">
      <div id="right-content">

        <h5><strong>PHP Version</strong></h5>
        <?php if ($checks['php']): ?>
          <p class="sucsess"><?= $msg['php_ok'] ?></p>
        <?php else: ?>
          <p class="error"><?= $msg['php_bad'] ?></p>
        <?php endif; ?>

        <br>

        <h5><strong>Required Extensions</strong></h5>
        <p>mysqli: <?= $checks['mysqli'] ? '<span class="sucsess">'.$msg['ext_ok'].'</span>' : '<span class="error">'.$msg['ext_bad'].'</span>' ?></p>
        <p>mbstring: <?= $checks['mbstring'] ? '<span class="sucsess">'.$msg['ext_ok'].'</span>' : '<span class="error">'.$msg['ext_bad'].'</span>' ?></p>
        <p>intl: <?= $checks['intl'] ? '<span class="sucsess">'.$msg['ext_ok'].'</span>' : '<span class="error">'.$msg['ext_bad'].'</span>' ?></p>
        <p>json: <?= $checks['json'] ? '<span class="sucsess">'.$msg['ext_ok'].'</span>' : '<span class="error">'.$msg['ext_bad'].'</span>' ?></p>
        <p>openssl: <?= $checks['openssl'] ? '<span class="sucsess">'.$msg['ext_ok'].'</span>' : '<span class="error">'.$msg['ext_bad'].'</span>' ?></p>

        <br>
        <p>Installer is currently set up for MySQLi. You can switch drivers later in <code>config/database.php</code> if needed.</p>

      </div>
    </div>
  </div>

  <hr class="hazar-separator">

  <p class="p-container">
    <a href="http://www.forgeigniter.com/forums" target="_blank"><span>Need Help ?</span></a>
    <?php if ($allGood): ?>
      <a href="dbsetup.php"><input type="button" id="submit" value="Next"></a>
    <?php endif; ?>
  </p>
</form>

</body>
</html>
