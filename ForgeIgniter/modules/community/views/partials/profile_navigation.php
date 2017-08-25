<?php if ($user['userID'] != $this->session->userdata('userID')): ?>

	<li><?php echo anchor('/messages/send_message/'.$user['userID'], 'Send a message', 'class="sendmessage"'); ?></li>

<?php else: ?>

	<li><?php echo anchor('/users/account#changeavatar', 'Change profile picture', 'class="changeavatar"'); ?></li>

<?php endif; ?>