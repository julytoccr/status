<?php echo $this->CustomPagination->show_links($page_url,$contexto=5) ?>

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
