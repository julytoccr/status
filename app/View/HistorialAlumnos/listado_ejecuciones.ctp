<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<table class="tabla_listado">
<tr>
	<th><?php echo __('nombre_problema');?></th>
	<th><?php echo __('nota');?></th>
	<th><?php echo __('fecha');?></th>
</tr>
<?php foreach ($data as $ejec): ?>
	<?php
		if ($es_profesor)
		{
			$url='/ejecuciones/profesor_mostrar_resultado/' . $ejec['EstEjecucion']['ejecucion_id'];
		}
		else
		{
			$url='/ejecuciones/alumno_mostrar_resultado/' . $ejec['EstEjecucion']['ejecucion_id'];
		}
    ?>
	<tr>
		<td>
		<?php echo $this->Html->link($ejec['Problema']['nombre'],$url); ?>
		</td>

		<td>
		<?php echo $this->Html->link($ejec['EstEjecucion']['nota'],$url); ?>
		</td>

		<td>
		<?php echo $this->Html->link($this->Formato->fecha_hora($ejec['EstEjecucion']['created']),$url); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<p>
<?php
	echo $this->Paginator->counter(array('format' => __('Página {:page} de {:pages}, mostrando {:current} resultados de {:count} en total')));
?>
</p>

<div class="paging">
<?php
	echo $this->Paginator->prev('< ' . __('anterior '), array(), null, array('class' => 'prev disabled'));
	echo $this->Paginator->numbers(array('separator' => ' '));
	echo $this->Paginator->next(__(' siguiente') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div>
