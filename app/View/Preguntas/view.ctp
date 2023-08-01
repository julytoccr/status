<div class="preguntas view">
<h2><?php  echo __('Pregunta'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Problema'); ?></dt>
		<dd>
			<?php echo $this->Html->link($pregunta['Problema']['nombre'], array('controller' => 'problemas', 'action' => 'view', $pregunta['Problema']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Orden'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['orden']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Enunciado'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['enunciado']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Respuesta'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['respuesta']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sintaxis Respuesta'); ?></dt>
		<dd>
			<?php echo $this->Html->link($pregunta['SintaxisRespuesta']['nombre'], array('controller' => 'sintaxis_respuestas', 'action' => 'view', $pregunta['SintaxisRespuesta']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sintaxis Parametro'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['sintaxis_parametro']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Expresiones Respuesta'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['expresiones_respuesta']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Expresiones Parametro'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['expresiones_parametro']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Peso'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['peso']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Intentos'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['intentos']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ayuda'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['ayuda']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Link1'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['link1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Link2'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['link2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mostrar Solucion'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['mostrar_solucion']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mensaje Correcto'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['mensaje_correcto']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mensaje Semicorrecto'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['mensaje_semicorrecto']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mensaje Incorrecto'); ?></dt>
		<dd>
			<?php echo h($pregunta['Pregunta']['mensaje_incorrecto']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Pregunta'), array('action' => 'edit', $pregunta['Pregunta']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Pregunta'), array('action' => 'delete', $pregunta['Pregunta']['id']), null, __('Are you sure you want to delete # %s?', $pregunta['Pregunta']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Preguntas'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Pregunta'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Problemas'), array('controller' => 'problemas', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Problema'), array('controller' => 'problemas', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sintaxis Respuestas'), array('controller' => 'sintaxis_respuestas', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sintaxis Respuesta'), array('controller' => 'sintaxis_respuestas', 'action' => 'add')); ?> </li>
	</ul>
</div>
