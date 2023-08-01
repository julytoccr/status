<?php
/**
 * Star Rating Helper
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 *
 * @link http://www.naffis.com/blog/articles/2006/08/31/rails-ajax-star-rating-system
 *
 * Copy star CSS file to app/webroot/css/star_rating/star_rating.css
 * and add the line "overflow: hidden;" to .star-rating
 *
 * @link http://komodomedia.com/blog/samples/star_rating/example2.htm
 *
 *
 *
 * Copy star Image file to app/webroot/css/star_rating/alt_star.gif
 * http://komodomedia.com//blog/samples/star_rating/alt_star.gif
 *
 * @package helpers
 *
 * Usage example: include the helper and in your view file:
 * echo $starRating->display('Post/rating',"/posts/rate/$id")
 * It will create a star rater for the field Post/rating. An AJAX call
 * will be made to /posts/rate/$id/$rate_value
 * ( /$rate_value is appended to the CakeUrl that you pass to display function )
 */

class StarRatingHelper extends Helper
{
	var $helpers=array('Html','Js','Ajax');

	/**
	 * Pixels of star width
	 */
	var $_star_width=25;

	var $ratings=array(
		array(	'title'=>'1 estrella de 5',
				'class'=>'one-star'),

		array(	'title'=>'2 estrellas de 5',
				'class'=>'two-stars'),

		array(	'title'=>'3 estrellas de 5',
				'class'=>'three-stars'),

		array(	'title'=>'4 estrellas de 5',
				'class'=>'four-stars'),

		array(	'title'=>'5 estrellas de 5',
				'class'=>'five-stars')
		);

	function _width($score){
		if ($score >=0 && $score <=5){
			$width = $score * $this->_star_width;
		}
		else{
			$width=0;
		}
		return $width;
	}

	/**
	 * Creates a star rater.
	 * @param mixed $value Model/field of the actual rate or the rate in float [0..5]
	 * 	Examples:
	 *   3.5
	 *   Post/rate
	 * @url Url to be called through AJAX
	 *  Example: if you need to rate the post id 3
	 *     $url='/post/rate/3'
	 *  Then when clicked the 5 stars, the url called will be /post/rate/3/5
	 * @return string The html code for the star rater
	 */
	function display($value,$url){
		static $index=0;
		$index++;
		echo $this->Html->css('star_rating/star_rating');
		$score=$value;
		$id="star_$index";
		$width=$this->_width($score);
		?>
		<ul class='star-rating' id='<?php echo $id?>'>
			<li class='current-rating' style='width:<?php echo $width?>px;'></li>
			<?php for ($i=0;$i<5;$i++):?>
				<li><a href='#' onclick="return false;" title='<?php echo $this->ratings[$i]['title']?>' class='star <?php echo $this->ratings[$i]['class']?>'><?php echo($i+1)?></a></li>
				<?php
					$call = $this->Ajax->remoteFunction( 
					array(
						'url' => $url . '/' . ($i+1),
						'success' => '$("#star_rating_gracias").show();
						var value = 25 * request;
						$(".current-rating").css("width",value);
						'
					));
					echo $this->Js->get("#$id .{$this->ratings[$i]['class']}")->event('click',$call);
					echo $this->Js->writeBuffer();
				?>
			<?php endfor;?>
		</ul>
		<?php
		return;
	}

	/**
	 * Change the number of stars of a displayed star rater
	 * @param string $dom_id The DOM ID of the star rater to change
	 * @param float $score The score from 0 to 5
	 *
	 * @return string HTML-Javascript Code
	 */
	function change($dom_id,$score)
	{
		//$this->_width($score);
	}

	/**
	 * Creates an effect of highlight in a displayed star rater
	 * @param string $dom_id The DOM ID of the star rater to change
	 * @param float $duration Duration of the effect in seconds
	 *
	 * @return string HTML-Javascript Code
	 */
	function highlight($dom_id,$duration=0.3){
		return $this->Js->get("#$dom_id .current-rating","#$dom_id")->effect('highlight',array('speed'=>$duration));
	}

}
?>
