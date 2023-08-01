<?php

class CustomPaginationHelper extends Helper
{
	var $helpers=array('Html');

	/**
	 * Muestra los links de paginación
	 *
	 * @param integer $total_pages Número de páginas total
	 * @param integer $contexto Número de links a mostrar por encima y por debajo	 *
	 * 		(el número total máximo de links es $contexto * 2 + 1)
	 * @param string $style Extra HTML style para los links
	 */
	function show_links($url,$contexto=5,$style='')
	{
		$num_page=$this->_View->viewVars['actual_page'];
		$total_pages=$this->_View->viewVars['total_pages'];

		if ($total_pages==1) return '';

		$html='<table border="0"><tr>';

		$inicial=$num_page - $contexto;
		if ($inicial < 1) $inicial=1;

		$final=$num_page + $contexto;


		if ($final > $total_pages) $final=$total_pages;

		if ($inicial != 1) $html.=$this->_link('<<',$url,1,$style);

		if ($num_page>$inicial) $html.=$this->_link('<',$url,$num_page - 1,$style);

		for ($i=$inicial;$i<=$final;$i++)
		{
			if ($i==$num_page)
			{
				$new_style=$style.'; color:black; ';
				$html.=$this->_link($i,$url,$i,$new_style);
			}
			else
			{
				$html.=$this->_link($i,$url,$i,$style);
			}
		}

		if ($num_page<$final) $html.=$this->_link('>',$url,$num_page + 1,$style);

		if ($final != $total_pages) $html.=$this->_link('>>',$url,$total_pages,$style);

		$html.='</tr></table>';
		return $html;
	}

	function _link($title,$url,$page,$style='')
	{
		$html='<td>';
		if (strstr($url,'?'))
		{
			$url="$url&page=$page";
		}
		else
		{
			$url="$url/?page=$page";
		}
		$html.=$this->Html->link($title,$url,array('style'=>$style));
		$html.='</td>';
		return $html;
	}
	
	function page(){
		return $this->_View->viewVars['actual_page'];
	}

}
