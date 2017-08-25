<?php if ($todaysActivity): $i=0; $title = ''; ?>

	<?php if ($recentActivity): ?>
		<h3>Recent Activity</h3>
		
		<ul>	
		<?php foreach($recentActivity as $visit): $style = ''; ?>
		<?php
			// get userdata
			$userdata = @unserialize($visit['userdata']);
			$style = '';
		?>	
			<li style="background: #FFFCDF;">
				<small><?php echo dateFmt($visit['date'], 'g:i a'); ?>:</small>
				<?php if ($userdata): ?>
					<?php if ($userdata['userID']): ?>
						<?php echo anchor('/admin/users/edit/'.$userdata['userID'], $userdata['firstName'].' '.$userdata['lastName']); ?>
				 		<?php echo ($userdata['dateCreated'] && strtotime($userdata['dateCreated']) >= strtotime('5 minutes ago')) ? '<strong>signed up</strong>' : 'returned'; ?>
				 	<?php else: ?>
			 		<?php echo anchor('/admin/webforms/tickets', $userdata['firstName'].' '.$userdata['lastName']); ?>
				 		<strong>submitted a web form</strong>
				 	<?php endif; ?>
			 	<?php else: ?>
				 	Someone visited 
				 <?php endif; ?>
			 		<?php echo (strtotime($visit['date']) >= strtotime('-2 minutes')) ? '<em>just now</em>' : ''; ?> 
			 		<?php echo ($visit['referer']) ? 'from '.anchor($visit['referer'], preg_replace('/http(s)?\:\/\/|www\.|\/(.*)$/i', '', htmlentities($visit['referer']))) : ''; ?>
			 	 	and looked at <?php echo ($visit['views']+1); ?> <?php echo ($visit['views']) ? 'pages' : 'page'; ?>.
			</li>
			
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

<h3>Today on Your Site</h3>	

	<ul>	
	<?php foreach($todaysActivity as $visit): $style = ''; ?>
	<?php
		// get userdata
		$userdata = @unserialize($visit['userdata']);
	?>	
		<li>
			<?php if ($userdata): ?>
				<small><?php echo dateFmt($visit['date'], 'g:i a'); ?>:</small>			
				<?php if ($userdata['userID']): ?>
					<?php echo anchor('/admin/users/edit/'.$userdata['userID'], $userdata['firstName'].' '.$userdata['lastName']); ?>
			 		<?php echo (strtotime($userdata['dateCreated']) >= strtotime(date("Y-m-d 00:00:00"))) ? '<strong>signed up</strong>' : 'returned'; ?>
			 	<?php else: ?>
			 		<?php echo anchor('/admin/webforms/tickets', $userdata['firstName'].' '.$userdata['lastName']); ?>
			 		<strong>submitted a web form</strong>
			 	<?php endif; ?>
			 	<?php echo ($visit['referer']) ? 'from '.anchor($visit['referer'], preg_replace('/http(s)?\:\/\/|www\.|\/(.*)$/i', '', htmlentities($visit['referer']))) : ''; ?> 	
		 	<?php else: ?>
			 	<?php echo $visit['guests'].' guest(s)'; ?> visited
			<?php endif; ?>
		 	and looked at <?php echo ($visit['views']+1); ?> <?php echo ($visit['views']) ? 'pages' : 'page'; ?>.
		</li>
		
	<?php endforeach; ?>
	</ul>
<?php else: ?>

	<p>Nothing has happened yet!</p>
	
<?php endif; ?>
<?php if ($yesterdaysActivity): $i=0; $title = ''; ?>

	<h3>Yesterday</h3>

	<ul>
	<?php foreach($yesterdaysActivity as $visit): $style = ''; ?>
	<?php
		// get userdata
		$userdata = @unserialize($visit['userdata']);
	?>	
		<li>
			<?php if ($userdata): ?>
				<small><?php echo dateFmt($visit['date'], 'g:i a'); ?>:</small>			
				<?php if ($userdata['userID']): ?>
					<?php echo anchor('/admin/users/edit/'.$userdata['userID'], $userdata['firstName'].' '.$userdata['lastName']); ?>
			 		<?php echo (strtotime($userdata['dateCreated']) >= strtotime(date("Y-m-d 00:00:00", strtotime('1 day ago')))) ? '<strong>signed up</strong>' : 'returned'; ?>
			 	<?php else: ?>
			 		<?php echo anchor('/admin/webforms/tickets', $userdata['firstName'].' '.$userdata['lastName']); ?>
			 		<strong>submitted a web form</strong>
			 	<?php endif; ?>
			 	<?php echo ($visit['referer']) ? 'from '.anchor($visit['referer'], preg_replace('/http(s)?\:\/\/|www\.|\/(.*)$/i', '', htmlentities($visit['referer']))) : ''; ?> 	
		 	<?php else: ?>
			 	<?php echo $visit['guests'].' guest(s)'; ?> visited
			<?php endif; ?>
		 	and looked at <?php echo ($visit['views']+1); ?> <?php echo ($visit['views']) ? 'pages' : 'page'; ?>.
		</li>
		
	<?php endforeach; ?>
	</ul>	
<?php endif; ?>
