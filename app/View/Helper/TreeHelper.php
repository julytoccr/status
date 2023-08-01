<?php
/**
 * Tree Helper
 *
 * @author RosSoft
 * @version 0.1
 *
 * Requires DestroyDrop Tree:
 * @link http://www.destroydrop.com/javascripts/tree/
 *
 * Api:
 * @link http://www.destroydrop.com/javascripts/tree/api/
 * @package helpers
 */

class TreeHelper extends Helper
{	
	var $helpers = array('Html', 'Tooltip');
	var $_trees=array();

	function afterRender()
	{
		$this->Html->script('dtree/dtree', array('block'=>'script'));
		$this->Html->css('dtree/dtree', null, array('block'=>'css'));		
	}

	function create($id)
	{
		$tree = new TreeAux($id, $this);
		$tree->img_folder = $this->webroot . 'img/dtree/';
		$this->_trees[] = $tree;
		return $tree;
	}
}

class TreeAux extends Object
{
	/**
	 * You can change it after creation
	 */
	var $img_folder=null;
	var $class='dtree';


	var $_id=null;
	var $_js_objname;
	var $_helper=null;
	var $_items=array();
	var $_events_items=array();

	function __construct($id, $helper)
	{
		$this->_id=$id;
		$this->_helper = $helper;
		$this->_js_objname="tree_{$this->_id}";
	}

	/**
	 * Creates a node
	 * @param boolean $is_folder True =>folder. False => item
	 * @param string $id Unique ID for the node
	 * @param string $parent The parent of the node. The root must be null
	 * @param string $name The text to show
	 * @param string $url Url to go on click
	 * @param string $tooltip Tooltip text
	 * @param string $target Window target for the link. Set to null to current window
	 * @param boolean $is_opened True if it's opened at page load
	 * @param string $img_closed Image closed
	 * @param string $img_opened Image opened. Null if you want the same
	 * @param boolean $is_checkbox True for a checkbox node
	 * @param string $class CSS Classname of the node
	 */
	function add($is_folder,$id, $parent, $name, $url=null, $tooltip=null, $target=null, $is_opened='', $img_closed=null, $img_opened=null, $is_checkbox=false,$class=null)
	{
		if ($parent===null) $parent=-1;
		else $parent=$this->_id . '_' . $parent;

		$id=$this->_id . '_' . $id;

		if ($is_folder===true) $type='F';
		else $type='P';

		if ($tooltip===null) $tooltip='';

		if ($target===null) $target='';

		if ($img_opened===null) $img_opened=$img_closed;

		$this->_items[]=array($type,$id,$parent,$name,$url,$tooltip,$target,$is_opened,$img_closed,$img_opened,$is_checkbox,$class);
	}

	/**
	 * Prints the tree
	 *
	 * @return string HTML Code for making the tree
	 */
	function draw()
	{
		$js="\n\n";
		$js.="var {$this->_js_objname}=new dTree('{$this->_js_objname}');\n";
		$js.="{$this->_js_objname}.imgFolder= '{$this->img_folder}';\n";
		foreach ($this->_items as $item)
		{
			$js.= $this->_js_objname.".add(";
			$js.= substr(json_encode($item), 1, -1);
			$js.= ");\n";
		}
		$js.="{$this->_js_objname}.draw();\n";
		$js = $this->_helper->Html->scriptBlock($js);

		$html="<div class=\"{$this->class}\" id=\"tree_{$this->_id}\">$js</div>\n";
		return $html;
	}

	function root($id,$name,$class=null)
	{
		$this->add($is_folder=true,$id, $parent=null, $name, $url=null, $tooltip=null, $target=null, $is_opened='1', $img_closed=null, $img_opened=null, $is_checkbox=false,$class);
	}

	function folder($id,$parent,$name,$is_opened=false,$img_closed='folder.gif',$img_opened='folderopen.gif',$is_checkbox=false,$class=null)
	{
		$this->add($is_folder=true,$id, $parent, $name, $url=null, $tooltip=null, $target=null, $is_opened, $img_closed, $img_opened, $is_checkbox,$class);
	}

	function item($id, $parent, $name, $url=null, $tooltip=null, $target=null, $img='page.gif', $is_checkbox=false,$class=null)
	{
		$this->add($is_folder=false,$id, $parent, $name, $url, $tooltip, $target, $is_opened=false, $img_closed=$img, $img_opened=$img, $is_checkbox,$class);
	}

	/**
	 * Construye el árbol de bloques con las
	 * asignaciones de cada bloque
	 * @param array $vars
	 */
	function arbol_bloques($bloques)
	{	
		foreach($bloques as $bloque){
			$bloque_id = 'bloque_' . $bloque['Bloque']['id'];
			$this->folder($bloque_id,
						'root',
						$bloque['Bloque']['nombre'],
						$is_opened=false,
						$img_closed='folder.gif',
						$img_opened='folderopen.gif',
						$is_checkbox=true,
						$class=null);

			foreach ($bloque['Problema'] as $prob){

				if ($prob['Agrupacion']['visible'])
					$img='2/page_visible.gif';
				else
					$img='2/page_oculto.gif';

				$nombre = $prob['Problema']['nombre'];

				if ($prob['ViewAgrupacionesPendientesNotificar']['agrupacion_id'])
					//Si está pendiente de ser notificado...
					$nombre.= ' (*)';

				$this->item('prob_' . $bloque_id . '_' . $prob['Problema']['id'],
						$bloque_id,
						$nombre,
						$url=null,
						$tooltip=null,
						$target=null,
						$img,
						$is_checkbox=true,
						$class=null);
			}
		}

	}

	/**
	 * Crea los tooltips con la información de los problemas
	 * de las carpetas
	 *
	 * @param array $vars Array de objetos. Contiene 'html'=>HtmlHelper y 'tree'=>TreeAux
	 * @param array $problemas Array de problemas
	 */
	 function crear_tooltips_problemas($problemas)
	 {
	 	$out = '';

		foreach ($problemas as $prob)
		{
			$id='problemas_carp_' . $prob['carpeta_id'] . '_' . $prob['id'];
			$cont=$prob['descripcion'] . '<br/>' .
				__('profesor').':' . $prob['profesor_nombre'] . '<br/>'.
				__('modificado'). $prob['modified'].'<br/>'.
				__('dificultad'). $prob['dificultad_nombre'].'<br/>'.
				($prob['published']!==null ? __('publicado') . $prob['published'] . '<br/>':'').
				($prob['expresiones_publicas'] ? __('expresiones_publicas'):'');

			$out .= $this->_helper->Tooltip->show($prob['nombre'],$cont,$id);
		}
		
		return $out;
	 }

	/**
	 * Crea los tooltips con la información de los problemas
	 * de las carpetas
	 *
	 * @param array $vars Array de objetos. Contiene 'html'=>HtmlHelper y 'tree'=>TreeAux
	 * @param array $bloques Array de bloques-problemas
	 * 	$bloques[$i]['Problema'][$j]['nombre']
	 */
	 function crear_tooltips_bloques($bloques)
	 {
		$out = '';

		foreach ($bloques as $bloque)
		{
			$bloque_id='bloque_' . $bloque['Bloque']['id'];
			foreach ($bloque['Problema'] as $prob)
			{
				$id='bloques_prob_' . $bloque_id . '_' . $prob['Problema']['id'];
				$cont=$prob['Problema']['descripcion'] . '<br/>' .
					__('tiempo_limite_minutos') . $prob['Agrupacion']['limite_minutos'] . '<br/>' .
					($prob['Agrupacion']['visible'] ? __('visible'):__('oculto'));

				$out .= $this->_helper->Tooltip->show($prob['Problema']['nombre'],$cont,$id);
			}
		}
		
		return $out;
	 }

	/**
	 * Crea los tooltips con la información de las carpetas
	 *
	 * @param array $vars Array de objetos. Contiene 'html'=>HtmlHelper y 'tree'=>TreeAux
	 * @param array $carpetas Array de carpetas
	 * @param string $prefijo Prefijo de los nombres de carpetas (en funcion del arbol: 'problemas','carpetas')
	 */
	 function crear_tooltips_carpetas($carpetas, $prefijo)
	 {
		$out = '';

		foreach ($carpetas as $c)
		{
			$id=$prefijo . '_carp_' . $c['Carpeta']['id'];
			$cont=__('profesor').':' . $c['Usuario']['nombre'] . '<br/>'.
				  ($c['Carpeta']['compartida'] ? __('compartida') . '<br/>':'');

			$out .= $this->_helper->Tooltip->show($c['Carpeta']['nombre'],$cont,$id);
		}
		
		return $out;
	 }


	/**
	 * Inserta los problemas en las carpetas correspondientes
	 *
	 * @param array $vars Array de objetos. Contiene 'html'=>HtmlHelper y 'tree'=>TreeAux
	 * @param array $problemas Array de problemas
	 * @param string $problema_class Clase HTML de los elementos "problemas"
	 * @param boolean $is_checkbox Si cierto, entonces la carpeta tendrá un checkbox
	 */
	function arbol_problemas($problemas,$problema_class,$is_checkbox,$action)
	{
		foreach ($problemas as $prob)
		{
			if ($prob['published']!==null)
				$img =  $prob['es_alias'] ? '2/pagepublink.gif' : '2/pagepub.gif';
			else
				$img =  $prob['es_alias'] ? '2/pagelink.gif' : '2/page.gif';

			//TODO: Solo propietario  / etc.
			$url=$this->_helper->Html->url('/problemas/'.$action.'/' . $prob['id']);

			$carp_id='carp_' . $prob['carpeta_id'];
			$this->item(	$carp_id . '_' . $prob['id'],
							$carp_id,
				  			$prob['nombre'],
				  			$url,
				  			$tooltip=null,
				  			$target=null,
				  			$img,
				  			$is_checkbox,
				  			$problema_class);
		}
	}

	/**
	 * Crea una carpeta especial en el arbol de carpetas
	 * La carpeta contiene las carpetas junto con sus problemas
	 * Ejemplo: la carpeta "Carpetas subscritas" contiene las carpetas "Variable discreta", "Variable contínua" y cada una
	 * de estas carpetas tendrán un checkbox si $is_checkbox=true
	 *
	 * @param array $vars Array de objetos. Contiene 'html'=>HtmlHelper y 'tree'=>TreeAux
	 * @param string $id Identificador de la carpeta a crear
	 * @param string $parent Identificador de la carpeta padre
	 * @param string $titulo Titulo de la carpeta a crear
	 * @param array $carpetas Array de subcarpetas
	 * @param boolean $is_checkbox Si cierto, entonces la carpeta tendrá un checkbox
	 */
	function arbol_carpetas($id, $parent, $titulo, $carpetas,$is_checkbox)
	{
		$this->folder($id,$parent,$titulo);

		foreach ($carpetas as $carp)
		{
			$carp_id='carp_' . $carp['id'];
			$this->folder(	$carp_id,
							$id,
							$carp['nombre'],
							$is_opened=false,
							$img_closed='folder.gif',
							$img_opened='folderopen.gif',
							$is_checkbox=$is_checkbox,
							$class='folder'
							);

		}
	}

	/**
	 * Opens all the folders
	 * @param boolean $enclose Returns the javascript code within <script> tag
	 */
	function open_all($enclose=true)
	{
		$js="{$this->_js_objname}.openAll();";
		return $this->_enclose($js,$enclose);
	}

	/**
	 * Closes all the folders
	 * @param boolean $enclose Returns the javascript code within <script> tag
	 */
	function close_all($enclose=true)
	{
		$js="{$this->_js_objname}.closeAll();";
		return $this->_enclose($js,$enclose);
	}

	/**
	 * Open a folder by its id
	 * @param string $folder_id
	 * @return string Javascript code
	 */
	function open($folder_id,$enclose=true)
	{
		$js="{$this->_js_objname}.abrirNodo('{$this->_id}_$folder_id');";
		return $this->_enclose($js,$enclose);
	}

	/**
	 * Returns the js code within /without <script> tag
	 * @param string $js Javascript code
	 * @param boolean $enclose true=>within. false=>without
	 * @return string
	 */
	function _enclose($js,$enclose)
	{
		if ($enclose)
		{
			$this->_helper->Html->scriptBlock($js);
		}
		else
		{
			return $js;
		}
	}
	
	function add_class($selector, $class){
		return "$('$selector').addClass('$class');";
	}
	
	function remove_class($selector, $class){
		return "$('$selector').removeClass('$class');";
	}
}
?>
