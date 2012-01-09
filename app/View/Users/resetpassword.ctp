 <div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php echo ('Reset Userpassword'); ?></legend>
		<h2><?php  echo __('User');?></h2>
	<dl>
		<dt><?php echo __('Username'); ?></dt>
		<dd>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
	</dl>
		<?php echo "<br>Do you really want to reset your password? A new password will be 
					created and send to the given email-address!<br>"; ?><br>
	</fieldset>
<?php echo $this->Form->end(('Submit'));?>
</div>