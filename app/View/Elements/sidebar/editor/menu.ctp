<?php 
	$this->start('script');
	echo $this->Ajax->ajax_sidebar_actions('EditorForm','editorlink');
	$this->end();
	
	echo $this->Html->link(__('menu_refrescar'),'/editor');
	echo $this->Tooltip->show(__('menu_refrescar'),__('explic_refrescar_carpetas_problemas'));
	
	echo $this->Html->link(__('menu_crear_carpeta'),'crear_carpeta',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_crear_carpeta'),__('explic_crear_carpeta'));

	echo $this->Html->link(__('menu_crear_problema'),'crear_problema',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_crear_problema'),__('explic_crear_problema'));

	echo $this->Html->link(__('menu_copiar_problemas'),'copiar_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_copiar_problemas'),__('explic_copiar_problemas'));

	echo $this->Html->link(__('menu_mover_problemas'),'mover_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_mover_problemas'),__('explic_mover_problemas'));

	echo $this->Html->link(__('menu_crear_alias_problemas'),'copiar_alias_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_crear_alias_problemas'),__('explic_crear_alias_problemas'));

	echo $this->Html->link(__('menu_publicar_problemas'),'publicar_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_publicar_problemas'),__('explic_publicar_problemas'));

	echo $this->Html->link(__('menu_compartir_carpetas'),'compartir_carpetas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_compartir_carpetas'),__('explic_compartir_carpetas'));

	echo $this->Html->link(__('menu_subscribir_carpetas'),'subscribir_carpetas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_subscribir_carpetas'),__('explic_subscribir_carpetas'));

	echo $this->Html->link(__('eliminar'),'eliminar_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('eliminar'),__('explic_eliminar_carpetas_problemas'));

	echo $this->Html->link(__('menu_buscar_problemas'),'buscar_problemas',array('class'=>'editorlink'));
	echo $this->Tooltip->show(__('menu_buscar_problemas'),__('explic_buscar_problemas'));
?>
