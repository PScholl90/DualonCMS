<div>
<p>Hi <?php echo $username?>,</p>
<p>Thank you for your registration at <?php $url?>. Your account has been created. Please click <?php echo $this->Html->link('here', $activationUrl);?> to activate your account.

<!-- build link --></p>
<p>Yours sincerly,<br>
<?php echo $url?></p>
</div>