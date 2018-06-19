<?php
echo '<?xml version="1.0" encoding="utf-8"?>' . "
";
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <channel>

    <title><?php echo $feed_name; ?></title>

    <link><?php echo $feed_url; ?></link>
    <description><?php echo $page_description; ?></description>
    <dc:language><?php echo $page_language; ?></dc:language>
    <dc:creator><?php echo $creator_email; ?></dc:creator>

    <dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>
    <admin:generatorAgent rdf:resource="<?php echo site_url() ?>" />

    <?php foreach($events as $entry): ?>

	    <item>

          <title><?php echo dateFmt($entry['eventDate'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'); ?> - <?php echo xml_convert($entry['eventTitle']); ?></title>
          <link><?php echo site_url('events/viewevent/'.$entry['eventID']); ?></link>
          <guid><?php echo site_url('events/viewevent/'.$entry['eventID']); ?></guid>

			<description><![CDATA[
				<?php echo $this->template->parse_body($entry['description'], TRUE, site_url('events/'.dateFmt($entry['eventDate'], 'Y/m').'/'.$entry['eventID'])); ?>
			]]></description>

        </item>


    <?php endforeach; ?>

</channel></rss>
