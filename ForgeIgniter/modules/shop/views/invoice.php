<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Halogy | Invoice</title>

	<style type="text/css">
		/* layout */
		body { font-size: 76%; background: #fff; font-family: "Lucida Grande", arial, sans-serif; margin-top: 40px; color: #171717; }
		div.content { width: 90%; margin: 0 auto; }

		/* content styles */
		div.content h1 { font-size: 2.2em; font-weight: normal; color: #171717; margin-bottom: 20px; }
		div.content h2 { font-size: 1.6em; font-weight: normal; color: #2194cd; margin-bottom: 10px; }
		div.content h3 { font-size: 1.4em; font-weight: normal; color: #171717; margin-bottom: 10px; }
		div.content h4 { font-size: 1.2em; font-weight: normal; color: #171717; margin-bottom: 20px; }
		div.content p { font-size: 1em; margin-bottom: 20px; line-height: 1.4em; }
		div.content small { color: #777; }
		div.content small strong { color: #777; }
		div.content ul { padding: 0 0 0 16px; list-style-position: outside; }
		div.content a { color: #2194cd; text-decoration: none; }
		div.content a:hover { color: #bbb; }
		div.content blockquote { color: #888; font-size: 1em; line-height: 1.8em; margin: 0 0 20px; padding: 10px 0 10px 40px; border-left: 3px solid #2194cd; }
		div.content li { padding: 0 0 8px 20px; }
		div.content hr { border: 0; color: #ccc; background-color: #ccc; height: 1px; width: 100%; text-align: left; margin: 0; }

		/* tables */
		table.default { width: 100%; font-size: 1em; border-collapse: collapse; margin-bottom: 20px; clear: both; }
		table.default th { padding: 6px; background: #f2f2f2; color: #777; text-align: left; border-bottom: 1px solid #ddd; }
		table.default td { padding: 6px; background: #fff; }
		table.default tr.header th { font-size: 1em; background: #eaeaea; color: #222; }
		table.default .narrow { width: 60px; }
		table.default .medium { width: 150px; }
	</style>

</head>
<body>

	<div class="content">

		<table class="default">
			<tr>
				<td><img src="<?php echo site_url('/images/halogy_reseller_logo.jpg'); ?>" alt="Logo" class="logo" /></td>
				<td width="140" align="right">
					<?php echo @nl2br($this->site->config['siteAddress']); ?>
					<br />
					<small>Tel.</small> <?php echo @$this->site->config['siteTel']; ?><br />

				</td>
			</tr>
		</table>

		<br />

		<table class="default">
			<tr>
				<td>
					<h1>Invoice</h1>

					<h3>
						<strong>Ref #:</strong> <?php echo $ref; ?><br />
						<strong>Date:</strong> <?php echo dateFmt($paymentDate, ((@$this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y')); ?>
					</h3>
				</td>
				<td width="140">
					<h3>Invoiced to:</h3>

					<p>
						ATTN: <?php echo $fullName; ?><br />
						<?php echo nl2br(trim($address)); ?><br />
						<?php echo $postcode; ?>
					</p>
				</td>
			</tr>
		</table>

		<br />

		<table class="default">
			<tr>
				<th>Item</th>
				<th width="140">Cost (<?php echo $currency; ?>)</th>
			</tr>
			<tr>
				<td><?php echo $this->site->config['siteName']; ?> Subscription Payment (#<?php echo $referenceID; ?>)</td>
				<td><?php echo currency_symbol(TRUE, $currency).number_format($paymentAmount, 2); ?></td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
			<tr>
				<td><strong>Total Amount:</strong></td>
				<td><strong><?php echo currency_symbol(TRUE, $currency).number_format($paymentAmount, 2); ?></strong> PAID</td>
			</tr>
			<tr>
				<td colspan="2"><hr /></td>
			</tr>
		</table>

	</div>

</body>
</html>
