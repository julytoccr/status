<?php 
	$this->start('script');
	echo $this->Ajax->ajax_sidebar_actions('BloqueProblemasForm','bloqueslink');
	$this->end();
	
	echo $this->Html->link(__('modificar'),'modificar_asignacion', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('modificar'),__('explic_modificar_problemas_bloques'));
	
	echo $this->Html->link(__('reasignar'),'reasignar', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('reasignar'),__('explic_reasignar_problemas_bloques'));

	echo $this->Html->link(__('menu_asignar'),'asignar_problemas', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_asignar'),__('explic_asignar_problemas_bloque'));

	echo $this->Html->link(__('menu_desasignar'),'desasignar_problemas', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_desasignar'),__('explic_desasignar_problemas_bloques'));

	echo $this->Html->link(__('menu_mostrar'),'mostrar_ocultar_problemas/1', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_mostrar'),__('explic_mostrar_problemas_bloques'));

	echo $this->Html->link(__('menu_ocultar'),'mostrar_ocultar_problemas/0', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_ocultar'),__('explic_ocultar_problemas_bloques'));

	echo $this->Html->link(__('menu_notificar'),'notificar_problemas/1', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_notificar'),__('explic_notificar_problemas_bloques'));

	echo $this->Html->link(__('menu_no_notificar'),'notificar_problemas/0', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_no_notificar'),__('explic_no_notificar_problemas_bloques'));

	echo $this->Html->link(__('menu_buscar_problemas'),'buscar_problemas', array('class'=>'bloqueslink'));
	echo $this->Tooltip->show(__('menu_buscar_problemas'),__('explic_buscar_problemas'));

	echo $this->Html->link(__('menu_volver_bloques'),'/bloques/index');
	echo $this->Tooltip->show(__('menu_volver_bloques'),__('explic_volver_bloques'));
?>
