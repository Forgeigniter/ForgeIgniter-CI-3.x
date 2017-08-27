<?php

/*  NOTES:
 | --------
 | Direct the person, give links to help docs or any info they may need.
 |
 | FILE: ForgeIgniter/install/complete.php
 | VERSION: 0.1
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
		  <h1>ForgeIgniter - Install Complete</h1>
		  <hr class="hazar-separator">
			<div class="row">
				<div class="col-md-4 colstyle" style="height:320px">			
					<div id="navside">
						<ul id="sidenav" class="nav nav-pills nav-stacked">
							<li class="active"><a href="#" style="background-color:rgb(50, 99, 50);" ><strong>1. The Checks</strong></a></li>
							<li class="active"><a href="#" style="background-color:rgb(50, 99, 50);" ><strong>2. Database Setup</strong></a></li>
							<li class="active"><a href="#" class="sidefun"><strong>3. Setup Complete</strong></a></li>
						</ul>
					</div>
				</div>
				<div class="col-md-8">
				  <div id="right-content">
					  <h5><strong>Login:</strong></h5>
					  <p> Username: superuser </p>
					  <p> Password: super123 </p>
					  <br />
					  <h5><strong>Help and Support:</strong></h5>
					  <p>
						  <a href="http://www.forgeigniter.com/support" target="_blank">Support</a><br />
						  <a href="http://www.forgeigniter.com/docs" target="_blank">User Guide</a><br />
						  <a href="http://www.forgeigniter.com/forums" target="_blank">Forums</a>
					  </p>
				  </div>
				</div>
			</div>
		  <hr class="hazar-separator">
		  <!-- Footer Form Links -->
		  <p class="p-container">
			<a href="http://www.forgeigniter.com/forums" target="_blank"><span>Need Help ?</span></a>
			<a href="../admin/login"><input type="button" name="submit" id="submit" value="Admin Login" action="../admin/login">
		  </p>
		</form>
		<!-- Ending mess -->
	</body>
</html>