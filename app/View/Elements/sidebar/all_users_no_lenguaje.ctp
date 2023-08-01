<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('User'); ?>
</p>

<div>
	<?php 
	echo '<p>Wellcome '.$this->Session->read('usuario.username').'</p>';
	echo "<p>There are no languages in the system</p>";
	echo $this->Html->link('Close session','/usuarios/logout');
	?>
</div>
