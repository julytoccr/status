<?php
	echo $this->Html->link(__('menu_eliminables'),'/problemas/eliminables');
	echo $this->Tooltip->show(__('menu_eliminables'),__('explic_eliminables'));
	echo $this->Html->link(__('menu_no_eliminables'),'/problemas/no_eliminables');
	echo $this->Tooltip->show(__('menu_no_eliminables'),__('explic_no_eliminables'));
?>
