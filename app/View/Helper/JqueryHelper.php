<?php
/**
 * jQuery Helper
 *
 * @package helpers
 */

class JqueryHelper extends AppHelper
{
	var $helpers=array('Html','Js');

	var $sSortAscending;
	var $sSortDescending;
	var $sFirst;
	var $sLast;
	var $sNext;
	var $sPrevious;
	var $buttonText;
	var $sEmptyTable;
	var $sInfo;
	var $sInfoEmpty;
	var $sLengthMenu;
	var $sLoadingRecords;
	var $sProcessing;
	var $sSearch;
	var $sZeroRecords;
	var $confirmRemove;
	var $tooltipRemove;
	var $tooltipEdit;
	var $tooltipConfirm;
	var $tooltipCancel;
	var $tooltipColumns;
	var $tooltipAdd;
	
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->sSortAscending = __('datatable_sSortAscending');
		$this->sSortDescending = __('datatable_sSortDescending');
		$this->sFirst = __('datatable_sFirst');
		$this->sLast = __('datatable_sLast');
		$this->sNext = __('datatable_sNext');
		$this->sPrevious = __('datatable_sPrevious');
		$this->buttonText = __('datatable_buttonText');
		$this->sEmptyTable = __('datatable_sEmptyTable');
		$this->sInfo = __('datatable_sInfo');
		$this->sInfoEmpty = __('datatable_sInfoEmpty');
		$this->sLengthMenu = __('datatable_sLengthMenu');
		$this->sLoadingRecords = __('datatable_sLoadingRecords');
		$this->sProcessing = __('datatable_sProcessing');
		$this->sSearch = __('datatable_sSearch');
		$this->sZeroRecords = __('datatable_sZeroRecords');
		$this->confirmRemove = __('datatable_confirmRemove');
		$this->tooltipRemove = __('datatable_tooltipRemove');
		$this->tooltipEdit = __('datatable_tooltipEdit');
		$this->tooltipConfirm = __('datatable_tooltipConfirm');
		$this->tooltipCancel = __('datatable_tooltipCancel');
		$this->tooltipColumns = __('datatable_tooltipColumns');
		$this->tooltipAdd = __('datatable_tooltipAdd');
	}

	function datatable($include_headers, $header_values, $rows, $sorting = NULL, $hidecolumns = NULL, $functionButton = FALSE, $editable = FALSE, $fieldNamesAndTypes = NULL, $addLink = "", $editLink = "", $deleteLink = "", $allowEditColumns = NULL)
	{
		if ($include_headers) {
			$this->_View->start('css');
			echo $this->Html->css('jquery.dataTables');
			echo $this->Html->css('jquery.tipTip');
			$this->_View->end();
			$this->_View->start('script');
			echo $this->Html->script('jquery/jquery-1.7.1.js');
			echo $this->Html->script('jquery/jquery.dataTables.min.js');
			echo $this->Html->script('jquery/jquery.dataTables.custom.js');
			echo $this->Html->script('jquery/jquery.dataTables.ColReorder.min.js');
			echo $this->Html->script('jquery/jquery.dataTables.ColVis.min.js');
			echo $this->Html->script('jquery/jquery.tipTip.min.js');
			$this->_View->end();
		}
		
		$jscode = "$(document).ready(function() {\r\n";
		
		if ($functionButton != NULL) {
			$functionLink = $this->Html->url($functionButton[0]);
			$functionTooltip = $functionButton[1];
			$functionClass = $functionButton[2];
			$functionOpenAjax = $functionButton[3];
			
			$jscode .= "	var functionButton = true;\r\n";
			$jscode .= "	var functionURL = \"" . $functionLink . "\";";
			$jscode .= "	var functionClass = \"" . $functionClass . "\";";
			$jscode .= "	var functionOpenAjax = \"" . $functionOpenAjax . "\";";
		}
		else {
			$functionLink = "";
			$functionTooltip = "";
			$functionClass = "";
			$functionOpenAjax = "";
			
			$jscode .= "	var functionButton = false;\r\n";
		}

		if ($allowEditColumns != NULL) {
			 $jscode .= "	var allowEditColumn = new Array(false,";
			 $jscode .= "" . (($allowEditColumns[0] === false) ? "false" : "true");
			 $limit = count($allowEditColumns);
			 for ($i = 1; $i < $limit; ++$i) {
				 $jscode .=", " . (($allowEditColumns[$i] === false) ? "false" : "true") . "";
			 }
			 $jscode .= ");\r\n";
		}
		else {
			$jscode .= "	var allowEditColumn = null;\r\n";
		}
		
		$isEditable = ($editable) ? "true" : "false";
		$jscode .= "	var editable = " . $isEditable . ";\r\n";
		
		if ($addLink != "") $addURL = $this->Html->url($addLink);
		else $addURL = "";
		if ($editLink != "") $editURL = $this->Html->url($editLink);
		else $editURL = "";
		if ($deleteLink != "") $deleteURL = $this->Html->url($deleteLink);
		else $deleteURL = "";
		$jscode .= "	var addURL = \"" . $addURL . "\";\r\n";
		$jscode .= "	var editURL = \"" . $editURL . "\";\r\n";
		$jscode .= "	var deleteURL = \"" . $deleteURL . "\";\r\n";
		
		if ($editable) {
			$arr=split('\.',$fieldNamesAndTypes[0][0]);
	
			$model=$arr[0];
			$field=$arr[1];

			$jscode .= "	var fieldNames = new Array(\"data[" . $model . "]" . "[" . $field . "]\"";
			$limit = count($fieldNamesAndTypes);
			for ($i = 1; $i < $limit; ++$i) {
				$arr=split('\.',$fieldNamesAndTypes[$i][0]);
		
				$model=$arr[0];
				$field=$arr[1];
				
				$jscode .= ", \"data[" . $model . "]" . "[" . $field . "]\"";
			}
			$jscode .= ");\r\n";
			
			$start = ($editable) ? 1 : 0;
			$jscode .= "	var fieldTypes = new Array(\"" . $fieldNamesAndTypes[$start][1] . "\"";
			$limit = count($fieldNamesAndTypes);
			for ($i = $start + 1; $i < $limit; ++$i) {
				$type = $fieldNamesAndTypes[$i][1];
				
				if ($type == "select") {
					$jscode .= ", new Array(new Array(\"" . $fieldNamesAndTypes[$i][2][0][0] . "\", \"" . $fieldNamesAndTypes[$i][2][0][1] . "\")";
					
					$limit2 = count($fieldNamesAndTypes[$i][2]);
					for ($j = 1; $j < $limit2; ++$j) {
						$jscode .= ", new Array(\"" . $fieldNamesAndTypes[$i][2][$j][0] . "\", \"" . $fieldNamesAndTypes[$i][2][$j][1] . "\")";
					}
					$jscode .= ")";
				}
				else {
					$jscode .= ", \"" . $fieldNamesAndTypes[$i][1] . "\"";
				}
			}
			$jscode .= ");\r\n";
		}
		else {
			$jscode .= "	var fieldNames = null;\r\n";
			$jscode .= "	var fieldTypes = null;\r\n";
		}
		
		$etiq_error = __('error'); //Error
		$etiq_ajax_error_peticion = __('ajax_error_peticion');
		$etiq_ajax_error_unexpectedanswer = __('ajax_error_unexpectedanswer');
		$etiq_ajax_error_filanoborrable = __('ajax_error_filanoborrable');
		
		$jscode .=
<<<HEREDOC

	var pluginParams = "{ \"editable\": \"" + editable + "\",";
	if (allowEditColumn != null)
	pluginParams	+= "  \"allowEditColumn\": " + JSON.stringify(allowEditColumn) + ",";
	if (fieldNames != null)
	pluginParams	+= "  \"fieldNames\": " + JSON.stringify(fieldNames) + ",";
	if (fieldTypes != null)
	pluginParams	+= "  \"fieldTypes\": " + JSON.stringify(fieldTypes) + ",";
	if (addURL != "")
	pluginParams	+= "  \"addURL\": \"" + addURL + "\",";
	if (editURL != "")
	pluginParams	+= "  \"editURL\": \"" + editURL + "\",";
	if (deleteURL != "")
	pluginParams	+= "  \"deleteURL\": \"" + deleteURL + "\",";
	pluginParams	+= "  \"functionButton\": \"" + functionButton + "\",";
	if (functionButton) {
		pluginParams	+= "  \"functionURL\": \"" + functionURL + "\",";
		pluginParams	+= "  \"functionClass\": \"" + functionClass + "\",";
		pluginParams	+= "  \"functionOpenAjax\": \"" + functionOpenAjax + "\",";
	}
 	pluginParams	+= "  \"addedTexts\": {";
	pluginParams	+= " 	  \"error\": \"{$etiq_error}\",";
	pluginParams	+= " 	  \"error_ajax_unexpected\": \"{$etiq_ajax_error_unexpectedanswer}\",";
	pluginParams	+= " 	  \"error_ajax_petition\": \"{$etiq_ajax_error_peticion}\",";
	pluginParams	+= "	  \"error_ajax_nonErasableRow\": \"{$etiq_ajax_error_filanoborrable}\",";
	pluginParams	+= "	  \"etiq_confirm_remove\": \"{$this->confirmRemove}\",";
	if (functionButton)
	pluginParams	+= "	  \"etiq_tooltip_function\": \"{$functionTooltip}\",";
	pluginParams	+= "	  \"etiq_tooltip_remove\": \"{$this->tooltipRemove}\",";
	pluginParams	+= "	  \"etiq_tooltip_edit\": \"{$this->tooltipEdit}\",";
	pluginParams	+= "	  \"etiq_tooltip_confirm\": \"{$this->tooltipConfirm}\",";
	pluginParams	+= "	  \"etiq_tooltip_cancel\": \"{$this->tooltipCancel}\",";
	pluginParams	+= "	  \"etiq_tooltip_columns\": \"{$this->tooltipColumns}\",";
	pluginParams	+= "	  \"etiq_tooltip_add\": \"{$this->tooltipAdd}\"";
	pluginParams	+= "  },";
	pluginParams	+= "  \"tableTexts\": {";
	pluginParams	+= " 	  \"oAria\": {";
	pluginParams	+= "		  \"sSortAscending\": \" - {$this->sSortAscending}\",";
	pluginParams	+= "		  \"sSortDescending\": \" - {$this->sSortDescending}\"";
	pluginParams	+= "	  },";
	pluginParams	+= "	  \"oPaginate\": {";
	pluginParams	+= "		  \"sFirst\": \"{$this->sFirst}\",";
	pluginParams	+= "		  \"sLast\": \"{$this->sLast}\",";
	pluginParams	+= "		  \"sNext\": \"{$this->sNext}\",";
	pluginParams	+= "		  \"sPrevious\": \"{$this->sPrevious}\"";
	pluginParams	+= "	  },";
	pluginParams	+= "	  \"oColVis\": {";
	pluginParams	+= "		  \"buttonText\": \"{$this->buttonText}\"";
	pluginParams	+= "	  },";
	pluginParams	+= "	  \"sEmptyTable\": \"{$this->sEmptyTable}\",";
	pluginParams	+= "	  \"sInfo\": \"{$this->sInfo}\",";
	pluginParams	+= "	  \"sInfoEmpty\": \"{$this->sInfoEmpty}\",";
	pluginParams	+= "	  \"sInfoFiltered\": \"\",";
	pluginParams	+= "	  \"sInfoPostFix\": \"\",";
	pluginParams	+= "	  \"sInfoThousands\": \",\",";
	pluginParams	+= "	  \"sLengthMenu\": \"{$this->sLengthMenu}\",";
	pluginParams	+= "	  \"sLoadingRecords\": \"{$this->sLoadingRecords}\",";
	pluginParams	+= "	  \"sProcessing\": \"{$this->sProcessing}\",";
	pluginParams	+= "	  \"sSearch\": \"{$this->sSearch}:\",";
	pluginParams	+= "	  \"sUrl\": \"\",";
	pluginParams	+= "	  \"sZeroRecords\": \"{$this->sZeroRecords}\"";
	pluginParams	+= " }
HEREDOC;
		if ($sorting !== NULL) {
			$jscode .= ", \\\"sortColumns\\\": [ ";
			
			$jscode .= " [ " . $sorting[0][0];
			for ($j = 1; $j < count($sorting[0]); ++$j) {
				if (is_string($sorting[0][$j])) {
					$jscode .= ", \\\"" . $sorting[0][$j] . "\\\"";
				}
				else {
					$jscode .= ", " . $sorting[0][$j];
				}
			}
			$jscode .= " ] ";
			for ($i = 1; $i < count($sorting); ++$i) {
				$jscode .= ", [ " . $sorting[$i][0];
				for ($j = 1; $j < count($sorting[$i]); ++$j) {
					if (is_string($sorting[$i][$j])) {
						$jscode .= ", \\\"" . $sorting[$i][$j] . "\\\"";
					}
					else {
						$jscode .= ", " . $sorting[$i][$j];
					}
				}
				$jscode .= " ] ";
			}
			$jscode .= " ] ";
		}

		if ($hidecolumns !== NULL || $editable || $functionButton) {
			$jscode .= ", \\\"columnProperties\\\": [ ";
			if ($hidecolumns !== NULL) {
				$jscode .= "{ \\\"bVisible\\\": false, \\\"bSearchable\\\": false, \\\"aTargets\\\": [ ";
				$jscode .= $hidecolumns[0];
				for ($i = 1; $i < count($hidecolumns); ++$i) {
					$jscode .= ", " . $hidecolumns[$i];
				}
				$jscode .= " ]";
			}
			if ($editable || $functionButton) {
				if ($hidecolumns !== NULL) $jscode .= ", ";
				$jscode .= "{ \\\"bSortable\\\": false, \\\"aTargets\\\": [ 0 ] }";
			}
			$jscode .= " ]";
		}

		$jscode .= 
<<<HEREDOC
}";

	var jsonObject = $.parseJSON(pluginParams)
	$(".datatable").customDataTable(jsonObject);

}
);
HEREDOC;
		$this->Html->scriptBlock($jscode,array('inline' => false));
		
		$ret = "<table class=\"datatable dataTable";
		if ($editable) {
			$ret .= " editable";
		}
		$ret .= "\" cellpadding=\"0\" cellspacing=\"1\">\n";
		$ret .= "	<thead>\n";
		$ret .= "		<tr>\n";

		if ($editable && count($header_values) > 0) {
			$ret .= "			<th class=\"addrow\"><a href=\"" . $addURL . "\">\n";
			$ret .= "			</a></th>\n";
		}
		else if ($functionButton) {
			$ret .= "			<th>\n";
			$ret .= "			</th>\n";
		}
		foreach ($header_values as $value) {
			$ret .= "			<th>" . $value . "\n";
			$ret .= "			</th>\n";
		}
		$ret .= "		</tr>\n";
		$ret .= "		<tr>\n";
		if (($editable && count($header_values) > 0) || $functionButton) {
			$ret .= "			<td class=\"editcolumns\"><input type=\"hidden\" class=\"search_init\" name=\"x\" value=\"x\" />\n";
			$ret .= "			</td>\n";
		}
		foreach ($header_values as $value) {
			// Remove accents
			setlocale(LC_ALL, "en_US.utf8");
			$field_name = iconv("utf-8", "ascii//TRANSLIT", $value);
			// Replace spaces
			$field_name = str_replace(" ", "_", $field_name);

			$ret .= "			<td><input type=\"text\" name=\"" . $field_name . "\" value=\"" . $value . "\" class=\"search_init\" />\n";
			$ret .= "			</td>\n";
		}
		$ret .= "		</tr>\n";
		$ret .= "	</thead>\n";
		$ret .= "	<tbody>\n";
		foreach ($rows as $row) {
			if ($editable || $functionButton) {
				$id = $row[0];
				unset($row[0]);
				
				$ret .= "		<tr id=\"row{$id}\">\n";
				$ret .= "			<td>";
				if ($functionButton) {
					$ret .= $this->Html->link($functionTooltip, $functionLink . "/" . $id);
				}
				if ($editable) {
					if ($functionButton) {
						$ret .= " | ";
					}
					$ret .= $this->Html->link(__('editar'), $editLink . "/" . $id) . " | " . $this->Html->link(__('eliminar'), $deleteLink . "/" . $id);
				}
				$ret .= "</td>\n";
			}
			else {
				$ret .= "		<tr>\n";
			}
			foreach ($row as $value) {
				$ret .= "			<td>" . $value . "</td>\n";
			}
			$ret .= "		</tr>\n";
		}
		$ret .= "	</tbody>\n";
		$ret .= "</table>\n";
		return $ret;
	}
}
?>
