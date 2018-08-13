<?php
/*
Plugin Name: Brizz WP Weather
Description: Insert social icons to your Wordpress posts.
Version: 1.0.0
Author: Aleksander Fesenko
Author URI: http://github.com/brizz79/
*/
/*  Copyright 2012  Oleksander Fesenko  (email : alexanderfesenko@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Usage: 
// [pogoda city='Oslo']
// Insert the name of the city in English into the city's field.


function shortcode_pogoda ($atts) {

    $atts = shortcode_atts( array(
        'city' => 'Dnipropetrovsk',
    ), $atts, 'pogoda' );

$api_key="bf56f4f13c1f42719dd13817181308";		//you API key from worldweatheronline.com
$num_of_days=10;								

$city = $atts['city'];
$city_string = urlencode($city);
$tp = 12; // time interval in hours

$premiumurl=sprintf('http://api.worldweatheronline.com/premium/v1/weather.ashx?key=%s&q=%s&tp=%s&num_of_days=%s&format=json', 
	$api_key, $city_string , $tp, intval($num_of_days));

$json_reply = file_get_contents($premiumurl);
$json=json_decode($json_reply);
$html = '';

  if(!isset($json->{'data'}->{'error'})) {
	
	$html = '<div class="pogoda_informer">';
	$date = DateTime::createFromFormat('Y-m-d', $json->{'data'}->{'weather'}[0]->{'date'});
	$date_string = $date->format('d.m.Y');
	$html .= '<div class="pogoda_header"><p>Погода на '.$date_string.'</p></div>';
	$html .= '<div class="pogoda_city"><p>'.$json->{'data'}->{'request'}[0]->{'query'}.'</p></div>';
	$html .= '<div class="pogoda_content"><div class="pogoda_img"><img src="'.$json->{'data'}->{'current_condition'}[0]->{'weatherIconUrl'}[0]->{'value'}.'"></img></div>';
	$html .= '<div class="pogoda_inf">';
	$html .= '<p>Днем: <span id="today_day">'.$json->{'data'}->{'weather'}[0]->{'hourly'}[1]->{'tempC'}.' °C</span></p>';
	$html .= '<p>Ночью: <span id="today_night">'.$json->{'data'}->{'weather'}[0]->{'hourly'}[0]->{'tempC'}.' °C</span></p>';
	$html .= '<p>Облачность: <span id="today_cloudcover">'.$json->{'data'}->{'current_condition'}[0]->{'cloudcover'}.' %</span></p></div></div>';
	// Cycle for average temperatures
	$sum_day = 0;
	$sum_night = 0;
	for($i=0;$i<10;$i++){
		$sum_day +=  $json->{'data'}->{'weather'}[$i]->{'hourly'}[1]->{'tempC'};
		$sum_night +=  $json->{'data'}->{'weather'}[$i]->{'hourly'}[0]->{'tempC'};
	}
	$date1 = DateTime::createFromFormat('Y-m-d', $json->{'data'}->{'weather'}[0]->{'date'});
	$date1_string = $date1->format('d.m.Y');
	$date2 = DateTime::createFromFormat('Y-m-d', $json->{'data'}->{'weather'}[9]->{'date'});
	$date2_string = $date2->format('d.m.Y');
	$html .= '<div class="pogoda_footer"><p><span id="days">Температура на '.$date1_string.' - '.$date2_string.':</span></p>';
	$html .= '<p>Днем среднее: '.(integer)($sum_day/10).' °C</p>';
	$html .= '<p>Ночью среднее: '.(integer)($sum_night/10).' °C</p></div>';
	$html .= '</div>';
  }
  else {
	$html = '<div class="pogoda_informer">';
	$html .= $json->{'data'}->{'error'}[0]->{'msg'};
	$html .= '</div>';
  }
	return $html; 
	
}
add_shortcode( 'pogoda', 'shortcode_pogoda' );

function brizz_wp_weather_styles()  
{  
    wp_register_style( 'custom-style', plugins_url( '/css/mystyles.css', __FILE__ ), array(), 'ver=1.0.0', 'all' );  
    wp_enqueue_style( 'custom-style' );  
}  
add_action( 'wp_enqueue_scripts', 'brizz_wp_weather_styles' );

?>
