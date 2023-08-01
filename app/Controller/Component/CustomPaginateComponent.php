<?php
/**
 * MyPagination Component
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class CustomPaginateComponent extends Component
{
	function startup($controller)
    {
    	$this->controller= $controller;
    }
    
	/**
	 * Devuelve el número total de páginas
	 *
	 * @param array $data Array de elementos a paginar  ($data[$i])
	 * @param integer $items_per_page Número de elementos de cada página
	 */
    function total_pages($data,$items_per_page)
    {
    	$items=count($data);
    	$inc=0;
    	if ($items % $items_per_page != 0)
    	{
    		$inc=1;
    	}
    	$total_pages=floor($items / $items_per_page) + $inc;
    	return $total_pages;
    }

	/**
	 * Devuelve un array con el tamaño de una página
	 *
	 * @param array $data Array de elementos a paginar  ($data[$i])
	 * @param integer $items_per_page Número de elementos de cada página
	 * @return array
	 */
    function paginate_data($data,$items_per_page)
    {
		if (isset($_GET['page'])) $num_page=$_GET['page'];
		else $num_page=1;

    	$page_data=array();

    	$initial=($num_page - 1)*$items_per_page;
    	$page_data=array_slice($data,$initial,$items_per_page);

    	$this->controller->set('total_pages',$this->total_pages($data,$items_per_page));
    	$this->controller->set('actual_page',$num_page);
    	return $page_data;
    }
}
?>
