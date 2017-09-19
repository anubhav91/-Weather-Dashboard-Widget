<?php
/*
Plugin Name: Weather Widget
Description: Weather Widget
Author: Abstain Solutions
Version:11
*/
wp_clear_scheduled_hook( 'weather_task_hook' );
function my_cron_schedules($schedules){
    if(!isset($schedules["45min"])){
        $schedules["45min"] = array(
            'interval' => 45*60,
            'display' => __('Once every 45 minutes'));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');

if (!wp_next_scheduled('weather_task_hook')) {
	wp_schedule_event( time(), '45min', 'weather_task_hook' );
}

add_action ( 'weather_task_hook', 'weather_cron_task_function' );
function weather_api_save()
{
	$request = 'http://api.openweathermap.org/data/2.5/weather?q=Brookvale,NSW,Australia&appid=64e504230084c0d1eced463bed45bd40';
    $response  = file_get_contents($request);	
	update_option('weather_api_response',$response);
}
function weather_cron_task_function() {
	weather_api_save();
}
function load_custom_wp_admin_style() 				// adding scripts
{
	$url  = plugin_dir_url( __FILE__ ) ;
    wp_register_style( 'weather_wp_admin_css', $url . 'weather-style.css', false, '1.0.0' );
    wp_enqueue_style( 'weather_wp_admin_css' );
	wp_enqueue_script( 'refresh.js',$url . 'refresh.js' ); //refresh div content		
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
add_action( 'wp_enqueue_scripts', 'load_custom_wp_admin_style' );
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
	return  number_format($fahrenheit) ;
}
//Kelvin to celsius equation
function kelvin_to_celsius($given_value)			
{
	$celsius=$given_value-273.15;
	return  number_format($celsius, 1) ;
}
function weather_child_function() 
{
	$response = get_option('weather_api_response');
	if(empty($response))
		weather_api_save(); 
	$response_updated = get_option('weather_api_response');
	date_default_timezone_set('Australia/Sydney');			// setting timezone
	$data = '';	
    $jsonobj  = json_decode($response_updated);						// decode data
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
	$data .= '<div class="center">Status : '.strtoupper($weather[0]->description).'</div>';
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
add_shortcode('weather_widget','weather_child_function');
?>
