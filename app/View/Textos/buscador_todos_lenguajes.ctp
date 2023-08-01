<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('gestionar_terminos'),
    "sidebar" => 'textos/textos'
));
$this->end();?>
<table width="100%"><tr>
<td width="90%" valign="top"><?php echo __('gestion_textos_etiqueta',array($etiqueta)); ?><?php echo $this->Html->link('Eliminar','/textos/eliminar_etiqueta/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$etiqueta_id.'/'.$lenguaje_actual.'/'.$pagina,null,'Seguro que quieres eliminar la etiqueta?');?></td>
<td width="10%" valign="top">




<?php echo $this->Html->link(__('volver'),'/textos/buscador_post/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$lenguaje_actual.'/?page='.$pagina,array("id"=>"paginacionactual"),null); ?>



</td>
</tr></table>






<table class="tabla_listado">
	<tr>
		<th></th>
		<th><?php echo __('lenguaje'); ?></th>
		<th><?php echo __('texto'); ?></th>
		<th><?php echo __('hereda_de'); ?></th>
		
	</tr>
<?php 

foreach ($texto as $e): 

?>
	<tr>
		<td> <?php echo $this->Html->link(__('modificar'),'/textos/modificar_texto/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$etiqueta_id.'/'.$e['lenguaje_id'].'/'.$e['lenguaje_id_session'].'/'.$lenguaje_actual.'/'.$pagina);

?> </td>
		<td><?php echo $e['lenguaje']?></td>
		<td><?php echo $e['texto']?></td>
		<td><?php echo $e['lenguaje_hereda_texto']?></td>
	</tr>
<?php endforeach; ?>
</table>

