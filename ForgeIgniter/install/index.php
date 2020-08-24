<?php

/*  NOTES:
 | --------
 | PHP should be 5.4.x preferably 5.6.x
 | MySQLi Is A must too really, for now MySQL setup is better than nothing.
 | Given that the config is a multidimensional array this should be easy.
 |
 | Switch check(easy to add too) and class needs to be done !
 | setup as a real module would be nice. 
 | 
 | Create Super User (main admin) needs to be created.
 |
 | Main System Configuration, like dir paths etc need to be created.
 |
 | FILE: ForgeIgniter/install/index.php
 | VERSION: 0.2
*/


error_reporting(0); //Note E_ALL = Blank Pages on some test should be 0 to get things rolling.

//require_once('includes/syschecks_class.php');

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
		
		<!-- Starting The Mess, one day we'll clean it up, shh, no one will notice. -->
		<form id="install_form">
		  <h1>ForgeIgniter - System Checks</h1>
		  <hr class="hazar-separator">
		  
		<div class="row">
			<div class="col-md-4 colstyle" style="height:320px">			
				<div id="navside">
					<ul id="sidenav" class="nav nav-pills nav-stacked">
						<li class="active"><a href="#" style="sidefun" ><strong>1. The Checks</strong></a></li>
						<li class="li-style"><strong>2. Database Setup</strong></a></li>
						<li class="li-style"><strong>3. Setup Complete</strong></a></li>
						
						<!-- Create Super User ?
						<li class="li-style"><strong>4. Admin Setup</strong></li>
						-->
						<!-- System Configuration ?
						<li class="li-style"><strong>3. Setup Configuration</strong></li>
						-->
					</ul>
				</div>
			</div>
			<div class="col-md-8">
			  <div id="right-content">
			  
              <h5><strong>PHP Version:</strong></h5>
			  <?php // Version Check
			  		if (version_compare(phpversion(), '7.0', '<')) {
						echo '<p class="error"> Please update PHP on the server to at least v7 to make things work correctly your current version is:'.PHP_VERSION.'</p>' ;
						$versionCheck = false;
					}else {
						echo '<p class="sucsess"> Great You Have 7.0 or Higher</p>';
						$versionCheck = true;
					}
					// && apache_get_version() ?? (Don't think this is needed.)
			  ?>
              
              <br/>
              
              <h5><strong>Database:</strong></h5>
			  <?php // Database Selection
			  ?>
              <p> Please note, the installer is only setup to work for mySQL at the moment. </p>
              <p> If you want to use another DB, don't let this discourage you as changing database.php dbdriver will work.</p>
              
			  <?php // Drivers, Extensions, plugin checks
			  ?>
			  </div>
			</div>
		</div>
		  
		  <hr class="hazar-separator">

		  <p class="p-container">
			<a href="http://www.forgeigniter.com/forums" target="_blank"><span>Need Help ?</span></a>
			
            <?php // If all is good then lets go
			  if ( $versionCheck == true ) {
			  	echo '<a href="dbsetup.php"><input type="button" name="submit" id="submit" value="Next" action="dbsetup.php">';
			  }
			?>
            
		  </p>
		</form>
		<!-- Ending mess -->

	</body>
</html>
