<?php
/*
Plugin Name: Weather Widget
Description: Weather Widget
Author: Abstain Solutions
*/
function load_custom_wp_admin_style() 				// adding scripts
{
	$url  = plugin_dir_url( __FILE__ ) ;
    wp_register_style( 'weather_wp_admin_css', $url . 'css/weather-style.css', false, '1.0.0' );
    wp_enqueue_style( 'weather_wp_admin_css' );
	wp_enqueue_script( 'refresh.js',$url . 'js/refresh.js' ); //refresh div content		
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
function weather_widget_main_function() 
{
	wp_add_dashboard_widget(
    'weather_dashboard_widget',         // Widget slug.
    'Weather Dashboard Widget',         // Title.
    'weather_child_function' 			// Display function.
    );	
}
add_action( 'wp_dashboard_setup', 'weather_widget_main_function' );
/**
 * Create the function to output the contents of our Dashboard Widget.
 */
//Kelvin to fahrenheit equation
function kelvin_to_fahrenheit($given_value)		
{
	$fahrenheit=9/5*($given_value-273.15)+32;
	return $fahrenheit ;
}
//Kelvin to celsius equation
function kelvin_to_celsius($given_value)			
{
	$celsius=$given_value-273.15;
	return $celsius ;
}
function weather_child_function() 
{
	date_default_timezone_set('Australia/Sydney');			// setting timezone
	$data = '';
	$request = 'http://api.openweathermap.org/data/2.5/weather?q=Brookvale,NSW,Australia&appid=64e504230084c0d1eced463bed45bd40';
    $response  = file_get_contents($request);				// read request 
    $jsonobj  = json_decode($response);						// decode data
	foreach($jsonobj as $key=>$value)
	{
		${$key} = $value;
	}
	$lon = $coord->lon;
	$lat = $coord->lat;
	$image_name = $weather[0]->icon;
	$data .= '
	<div id="parent_weather" class="parent_weather"><div><h2><center>'.$name.'</center></h2></div>';
	$data .= '<div class="center"><img src="http://openweathermap.org/img/w/'.$image_name.'.png"></div>';
	$data .= '<div class="center">Status : '.ucfirst($weather[0]->description).'</div>';
	$data .= '<div class="center">Temprature (Fahrenheit) : '.kelvin_to_fahrenheit($main->temp).' F</div>';
	$data .= '<div class="center">Temprature (Celsius) : '.kelvin_to_celsius($main->temp).' .C</div>';
	$data .= '<ul class="cloud"><li>Clouds : '.$clouds->all.'%</li>';
	$data .= '<li class="right">Humidity : '.$main->humidity.'%</li>';
	$data .= '<li>Wind : '.$wind->speed.'m/s</li>';
	$data .= '<li class="right">Pressure : '.$main->pressure.'m/s</li>';
	$data .= '<li>Sunrise : '.date('m/d/Y h:i:s A',$sys->sunrise).'</li>';
	$data .= '<li class="right">Sunset : '.date('m/d/Y h:i:s A', $sys->sunset).'</li></ul></div>';
	echo $data;
}
?>
