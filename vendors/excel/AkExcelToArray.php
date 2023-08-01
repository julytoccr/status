<?php
/**
 * Modified from Akelos Framework
 * @license GPL
 */
class AkExcelToArray extends Object
{
	/**
	 * The first column of data.
	 * @var integer
	 */
	var $first_column=0;

	/**
	 * The first row of data.
	 * @var integer
	 */
	var $first_row=0;

	/**
	 * Source content. Must be set if $source_file=null
	 * @var string The content in XLS format
	 */
	var $source=null;

	/**
	 * Source file
	 * @var string The filename of an XLS
	 */
	var $source_file=null;

	/**
	 * @access private
	 */
	var $handler=null;

	/**
	 * Static member function for converting a file to array
	 * @param string $source_file
	 * @param integer $first_column The first column of data
	 * @return array
	 */
	function convert_file($source_file,$first_row=0,$first_column=0)
	{
		$converter=& new AkExcelToArray();
		$converter->first_row=$first_row;
		$converter->first_column=$first_column;
		$converter->source_file=$source_file;
		return $converter->convert();
	}

    function convert()
    {
    	$this->init();
        $this->handler->read($this->source_file);

        $result = array();
        for ($i = $this->first_row; $i < $this->handler->sheets[0]['numRows']; $i++)
        {
	        for ($j = $this->first_column; $j < $this->handler->sheets[0]['numCols']; $j++)
	        {
                //$result[$i - $this->first_row][$j - $this->first_column] = isset($this->handler->sheets[0]['cells'][$i][$j]) ? utf8_encode($this->handler->sheets[0]['cells'][$i][$j]) : null;
                //$result[$i - $this->first_row][$j - $this->first_column] = isset($this->handler->sheets[0]['cells'][$i][$j]) ? utf8_encode($this->handler->sheets[0]['cells'][$i][$j]) : null;
                $result[$i - $this->first_row][$j - $this->first_column] = isset($this->handler->sheets[0]['cells'][$i][$j]) ? $this->handler->sheets[0]['cells'][$i][$j] : null;
	        }
        }
        $this->delete_source_file ? unlink($this->source_file) : null;
        return $result;
    }

    function init()
    {
        if(! $this->handler)
        {
            App::import('Vendor', 'excel/reader');
            $this->handler = new Spreadsheet_Excel_Reader();
            $this->handler->setRowColOffset(0);
            $this->handler->setOutputEncoding('UTF-8');
            $this->handler->setUTFEncoder('iconv');
        }

        if(! $this->source_file)
        {
        	$this->source_file= tempnam(TMP,'excel_');
           	file_put_contents($this->source_file,$this->source);
            $this->delete_source_file = true;
            $this->keep_destination_file = empty($this->keep_destination_file) ? (empty($this->destination_file) ? false : true) : $this->keep_destination_file;
        }else{
            $this->delete_source_file = false;
            $this->keep_destination_file = true;
        }
    }
}

?>
