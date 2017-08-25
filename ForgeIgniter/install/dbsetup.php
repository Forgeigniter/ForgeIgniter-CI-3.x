<?php

error_reporting(0); //Setting this to E_ALL showed that that cause of not redirecting were few blank lines added in some php files.

$db_config_path = 'config/database.php';

// Only load the classes in case the user submitted the form
if($_POST) {

	// Load the classes and create the new objects
	require_once('includes/core_class.php');
	require_once('includes/database_class.php');

	$core = new Core();
	$database = new Database();


	// Validate the post data
	if($core->validate_post($_POST) == true)
	{

		// First create the database, then create tables, then write config file
		if($database->create_database($_POST) == false) {
			$message = $core->show_message('error',"The database could not be created, please verify your settings.");
		} else if ($database->create_tables($_POST) == false) {
			$message = $core->show_message('error',"The database tables could not be created, please verify your settings.");
		} else if ($core->write_config($_POST) == false) {
			$message = $core->show_message('error',"The database configuration file could not be written, please chmod halogy/config/database.php file to 777");
		}

		// If no errors, redirect to next page
		if(!isset($message)) {
		  $redir = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
      $redir .= "://".$_SERVER['HTTP_HOST'];
      $redir .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
      $redir = str_replace('install/','',$redir); 
			header( 'Location: ' . $redir . 'install/complete.php' ) ;
		}

	}
	else {
		$message = $core->show_message('error','Not all fields have been filled in correctly.');
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Install | ForgeIgniter</title>
		<link href="../../static/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="includes/style.css" />
	</head>
	<body>
	
    <?php if(is_writable($db_config_path)){?>
		
		<form id="install_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		  <h1>ForgeIgniter - MYSQL Setup</h1>
		  <hr class="hazar-separator">
		  
		<div class="row">
			<div class="col-md-4 colstyle" style="height:320px">			
				<div id="navside">
					<ul id="sidenav" class="nav nav-pills nav-stacked">
						<li class="active"><a href="#" style="background-color:rgb(50, 99, 50);" ><strong>1. The Checks</strong></a></li>
						<li class="active"><a href="#" style="sidefun" ><strong>2. Database Setup</strong></a></li>
						<li class="li-style"><strong>3. Setup Complete</strong></a></li>
						
						
						<!-- Create Super User ?
						<li class="li-style"><strong>Admin Setup</strong></li>
						-->
						<!-- System Configuration ?
						<li class="li-style"><strong>Setup Configuration</strong></li>
						-->
					</ul>
				</div>
			</div>
			<div class="col-md-8">
			  <div id="right-content">
			  <?php if(isset($message)) {echo '<p class="error">' . $message . '</p> <br />';}?>
				  <p>
					<label for="hostname">Hostname</label>
					<input type="text" name="hostname" id="hostname" value="localhost">
				  </p>
				  <p>
					<label for="database">Database Name</label>
					<input type="text" name="database" id="database">
				  </p>
				  <p>
					<label for="username">Database Username</label>
					<input type="text" name="username" id="username">
				  </p>
				  <p>
					<label for="password">Database Password</label>
					<input type="password" name="password" id="password">
				  </p>
			  </div>
			</div>
		</div>
		  
		  <hr class="hazar-separator">

		  <p class="p-container">
			<a href="http://www.forgeigniter.com/forums" target="_blank"><span>Need Help ?</span></a>
			<input type="submit" name="submit" id="submit" value="Next">
		  </p>
		</form>
		
	<?php } else { ?>
      <p class="error">Please make sure config/database.php file writable. <strong>Example</strong>:<br /><br /><code>chmod 777 forgeigniter/config/database.php</code> <br /> if this is true then double check username and password.</p>
	<?php } ?>
	

	</body>
</html>
