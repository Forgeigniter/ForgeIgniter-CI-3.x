<?php header("HTTP/1.1 404 Not Found"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
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

				<h1>Slight problem...</h1>

				<?php echo $message; ?>

			</center>

		<div class="spacer"><!-- --></div>

	</div><!--/main-->

</div><!--/container-->

</body>
</html>