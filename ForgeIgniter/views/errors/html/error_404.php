<?php header("HTTP/1.1 404 Not Found"); 
defined('BASEPATH') OR exit('No direct script access allowed');?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="refresh" content="12; url=/" />
	<title><?php echo $heading; ?></title>
	<style type="text/css">
		body { font-size: 76%; font-family: "Lucida Grande", arial, san-serif; margin: 50px 0 0; color: #171717; }
		div.container { width: 600px; margin: 0 auto; padding: 0; clear: both; z-index: 0;  }
		div#content { display: inline; float: left; width: 100%; padding: 40px 0 20px; clear: both; }
		div.content h1 { font-size: 3em; font-weight: normal; color: #171717; margin: 0 0 40px; }
		div.content h3 { font-size: 2em; font-weight: normal; color: #171717; margin: 0 0 30px; }
		div.content p { font-size: 1.2em; color: #999; margin: 0 0 10px; line-height: 1.4em; }
		div.content img { font-size: 1.2em; margin: 50px 0 20px; line-height: 1.4em; }		
		div.content ul { padding: 0 0 0 16px; list-style-position: outside; }
		div.content a { color: #2194CD; text-decoration: none; }
		div.content a:hover { color: #bbb; }
	</style>
</head>
<body>

<!--container-->
<div id="container">

	<div id="main" class="narrow">
		<div id="content" class="content">

			<center>

				<h1><?php echo $_SERVER['REQUEST_URI'] ?> Page not found...</h1>

				<h3><strong>Sorry,</strong> this page doesn't exist yet.</h3>

				<p>We suggest you go back to the <a href="<?php echo site_url() ?>">Home Page</a>.</p>
				
				<p> or just wait, we'll point you in the right direction. </p>
				
				<p> <?php echo $message; ?> </p>

				<img src="<?php echo site_url('static/images/fi-logo.jpg'); ?>" alt="ForgeIgniter">

			</center>

		<div class="spacer"><!-- --></div>

	</div><!--/main-->

</div><!--/container-->

</body>
</html>