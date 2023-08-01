<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('idiomas'),
    "sidebar" => 'lenguajes/idiomas'
));
$this->end();?>
<?php echo $this->Html->link(__('volver'),'/textos/ver_notificaciones_correcciones/');?>
<br>
<table class="tabla_listado">
	

	<tr>
		<td><?php echo __('fecha'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['fecha'] ?></td>
	</tr>
	
	<tr>
		<td><?php echo __('form_login'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['login'] ?></td>
	</tr>
			
	<tr>
		<td><?php echo __('form_email'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['mail'] ?></td>
	</tr>
	<tr>
		<td width="20%"><?php echo __('lenguaje'); ?></td>
		<td width="80%"><?php echo $correccion['CorreccionTextos']['lenguaje'] ?></td>
	</tr>
	<tr>
		<td width="20%"><?php echo __('url'); ?></td>
		<td width="80%"><?php echo $correccion['CorreccionTextos']['url'] ?></td>
	</tr>
	<tr>
		<td><?php echo __('form_texto_actual'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['texto_actual'] ?></td>
	</tr>
	
	<tr>
		<td><?php echo __('form_sugerencia'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['texto_correccion'] ?></td>
	</tr>		
		
	<tr>
		<td><?php echo __('form_notas_adicionales'); ?></td>
		<td><?php echo $correccion['CorreccionTextos']['notas_adicionales'] ?></td>
	</tr>
	
	<tr>
		<td><?php echo __('estado_notificacion'); ?></td>
		<?php 
		if ( $correccion['CorreccionTextos']['estado']=='Revisada')
		{
			echo '<td>'.__('revisada').'</td>';
		}else
		{
			echo '<td>'.__('no_revisada').'</td>';
		}
		?>
	</tr>


</table>

<?php echo $this->Html->link(__('marcar_como_revisada'),'/textos/marcar_revision_notificacion/'.$correccion['CorreccionTextos']['id']);?>
<br>
<?php echo $this->Html->link(__('eliminar_notificacion'),'/textos/eliminar_notificacion/'.$correccion['CorreccionTextos']['id']);?>
