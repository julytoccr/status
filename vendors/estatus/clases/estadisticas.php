<?php
/**
 * Estadisticas
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */
class Estadisticas extends Object
{
	function stdev($array)
	{
		  //variable and initializations
		  $the_standard_deviation = 0.0;
		  $the_variance = 0.0;
		  $the_mean = 0.0;
		  $the_array_sum = array_sum($array); //sum the elements
		  $number_elements = count($array); //count the number of elements

		  //calculate the mean
		  if($number_elements > 0)
		  	$the_mean = $the_array_sum / $number_elements;

		  //calculate the variance
		  for ($i = 0; $i < $number_elements; $i++)
		  {
		    //sum the array
		    $the_variance = $the_variance + ($array[$i] - $the_mean) * ($array[$i] - $the_mean);
		  }

		  if ($number_elements>1)
		  {
		  	$the_variance = $the_variance / ($number_elements - 1);
		  }
		  else
		  {
		  	$the_variance=0;
		  }


		  //calculate the standard deviation
		  $the_standard_deviation = pow( $the_variance, 0.5);

		  //return the deviation
		  return $the_standard_deviation;
	}

	function mean($array)
	{
		  $the_array_sum = array_sum($array); //sum the elements
		  $number_elements = count($array); //count the number of elements

		  $the_mean = 0.0;
		  //calculate the mean
		  if($number_elements > 0)
		  	$the_mean = $the_array_sum / $number_elements;
		  return $the_mean;
	}
}
?>