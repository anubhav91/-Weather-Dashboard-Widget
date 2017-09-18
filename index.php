<?php
/*
Plugin Name: Weather Widget
Description: Weather Widget
Author: Abstain Solutions
*/
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
function weather_child_function() 
{
	?>
	<script>
	function api()				// function to fetch data from api 
	{
		jQuery.ajax({
		  type:"post",
		  url:"http://api.openweathermap.org/data/2.5/weather?q=Brookvale,NSW,Australia&mode=html&appid=64e504230084c0d1eced463bed45bd40",
		  datatype:"html",
		  success:function(data)
		  {
			 jQuery('.widget_weather').html(data);		//added data in widget_weather class
		  }
		});
	}
	jQuery(document).ready(function(){
	api();												
	setInterval(function()									
	{ 
    api();
	},45*60000);//time in milliseconds 45 minutes
	}) 
	</script>
	<div class="widget_weather"></div>
<?php
}
?>