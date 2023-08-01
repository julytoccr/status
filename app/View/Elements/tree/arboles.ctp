<?php
	//	$this->_viewVars['disable_ie_js']=true;
?>

<table class="arboles">
	<tr>
		<td>
			<br/>
			<?php echo $this->Html->link(__('plegar_todo'),'#',array('onclick'=>'tree_problemas.closeAll();return false;'))?>
			<br/>
			<?php 
			echo $this->Html->link(__('desplegar_todo'),'#',array('onclick'=>'tree_problemas.openAll();return false;'));
			$t = $this->Tree->create('problemas');
			$t->root('root',__('problemas'));
			$t->arbol_carpetas('propias', 'root', __('carpetas_propias'), $propias, false);
			$t->arbol_carpetas('subs','root', __('externas_subscritas'), $subscritas, false);
			$t->arbol_carpetas('nosubs','root', __('externas_no_subscritas'), $no_subscritas, false);
			$t->arbol_problemas($problemas,'problema',true,$action);
			echo $t->draw();
			echo $t->crear_tooltips_problemas($problemas);
			echo $t->crear_tooltips_carpetas($carpetas,'problemas');
			echo $t->close_all();
			?>
		</td>
		<td>
			<br/>
			<?php
				if(isset($bloques)) $v = 'bloques';
				else  $v = 'carpetas'
			?>
			<?php echo $this->Html->link(__('plegar_todo'),'#',array('onclick'=>'tree_'.$v.'.closeAll();return false;'))?>
			<br/>
			<?php echo $this->Html->link(__('desplegar_todo'),'#',array('onclick'=>'tree_'.$v.'.openAll();return false;'))?>
			<?php
			$t= $this->Tree->create($v);
			$t->root('root',__($v));
			if(isset($bloques)){
				$t->arbol_bloques($bloques);
				echo $t->crear_tooltips_bloques($bloques);
			}else{
				$t->arbol_carpetas('propias', 'root', __('carpetas_propias'), $propias, true);
				$t->arbol_carpetas('subs','root', __('externas_subscritas'), $subscritas, true);
				$t->arbol_carpetas('nosubs','root', __('externas_no_subscritas'), $no_subscritas, true);
			}
			echo $t->draw();
			echo $t->close_all();
			?>
		</td>
	</tr>
</table>

