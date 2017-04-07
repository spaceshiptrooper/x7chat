<?php $errors = $x7->get_messages('error'); ?>
<?php if($errors): ?>
	<ul class='errors'>
		<?php foreach($errors as $error): ?>
			<li class='error'><?php $esc($error); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php $notices = $x7->get_messages('notice'); ?>
<?php if($notices): ?>
	<ul class='notices'>
		<?php foreach($notices as $notice): ?>
			<li class='notice'><?php $esc($notice); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>