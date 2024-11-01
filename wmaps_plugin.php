<?php
/*
Plugin Name: WMaps
Plugin URI:  http://www.grupomayanvacations.com/wmaps 
Description: Plug-in that enable your wordpress blog to use easily Google Maps in your posts.
Author: Demian Rice
Version: 1.0
Author URI: http://www.grupomayanvacations.com/
*/

// ******************************************
// *********** INSTALACION ******************
// ******************************************


register_activation_hook(__FILE__,'wonkasmaps_install');


$wmaps_maps_table   = "wonkasmaps";
$wmaps_points_table = "wonkasmaps_points";


function wonkasmaps_install () {
   
   global $wpdb;
   
   $wmaps_maps_table   = "wonkasmaps";
   $table_name = $wpdb->prefix . $wmaps_maps_table;
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
   {
      
      $sql = "CREATE TABLE " . $table_name . " (
		  map_id int(10) unsigned NOT NULL AUTO_INCREMENT,
		  map_description VARCHAR(55) NOT NULL,
		  map_latitud float(10,6) NOT NULL,
		  map_longitud float(10,6) NOT NULL,
		  map_zoom float NOT NULL,
		  map_width smallint(5) unsigned NOT NULL,
		  map_height smallint(5) unsigned NOT NULL,
		  text_align VARCHAR(10) NOT NULL,
		  UNIQUE KEY map_id (map_id)
	  );"; 
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   		dbDelta($sql);
   }

    
    
    $wmaps_points_table = "wonkasmaps_points";
    $table_Points = $wpdb->prefix . $wmaps_points_table;
    if($wpdb->get_var("show tables like '$table_Points'") != $table_Points) {
      
      $sql = "CREATE TABLE " . $table_Points . " (
	  id_point int(10) unsigned NOT NULL AUTO_INCREMENT,
	  point_description VARCHAR(55) NOT NULL,
	  point_latitud float(10,6) NOT NULL,
	  point_longitud float(10,6) NOT NULL,
	  map_id int(10) NOT NULL,
	  UNIQUE KEY id_point (id_point)
	);";
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
     dbDelta($sql);  
   };
   
}


// *********** INSTALACION ******************
include("wmaps_menu_options.php");
include ("wmaps_map_list.php");


// *********** PARSEO DEL CONTENIDO DE LOS POST PARA SUSTITUIR EL MAPA ***********

$mapsEncontrados;
$indice = 0;
function wmaps_setpost_maps($contenido_post)
{	
	global $mapsEncontrados;
	global $wpdb;
	global $wmaps_maps_table;
	global $indice;
	// $mapa = '<div id="wmaps_map1"></div>';
	
	preg_match_all('/\[(.*?)getmymap(.*?)id(.*?)=(.*?)"(.*?)"(.*?)\]/', $contenido_post, $MapasEnPost);
	
	
	foreach ($MapasEnPost[0] as $Mapa)
	{
		$pcomilla = strpos ($Mapa,'"');
		$ucomilla = strrpos($Mapa,'"');
		$id = substr($Mapa,($pcomilla+1),($ucomilla-1) - ($pcomilla));
		
		
		$objMapa = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix .  $wmaps_maps_table . 
							          " WHERE map_id = '$id'");
		if ($objMapa[0])
		{	
			$mapsEncontrados[$indice] = $objMapa[0];
			$strDivMapa = '<div style="float:' . $objMapa[0]->text_align . ';">' . "\n" . 	
							'<div id="wmap' . $id . '_canvas_' . $indice .'" style="border: 1px solid black; ' . "\n" . 
								      'width: ' . $objMapa[0]->map_width .'px; ' . "\n" . 
							   		  'height: ' . $objMapa[0]->map_height .'px; '. "\n" . 
							   		  '"></div>'. "\n" . 
							 '<div style="font-size:10px; font-weight:bold; color:black;">' ."\n"   
								 . $objMapa[0]->map_description . "<br/>" .
								'<font style="font-size:6px;">Created by ' . 
									'<a href="http://www.grupomayanvacations.com" target="_TOP" title="grupo mayan">wmpas plugin</a></font>' . 
							 '</div>' . "\n" . 
							 	
					     '</div>'. "\n" ; 
			$contenido_post = str_replace($Mapa,$strDivMapa,$contenido_post);
			$indice++;
		}
		
	}; 
	
	return $contenido_post;
}

function wmaps_insert_gmapskey()
{
	echo '<!-- **** Google Maps **** -->
	<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . get_option("wmaps_googlemaps_key") . '">
	</script>' . "\n";
	
	echo '<script type="text/javascript" src="' . get_option("siteurl") . '/wp-content/plugins/wonkas_maps/wmaps.js">' .
		 '</script>';
	
}

// **********************************************
// ***** Carga todos los mapas encontrados ******
// **********************************************
function wmaps_footer()
{	
	global $mapsEncontrados;
	global $wmaps_points_table;
	global $wpdb;
	
	if (!$mapsEncontrados)
	{
		return;
	}
	else if (count($mapsEncontrados) <= 0)
	{
		return;
	}
	
	echo '<script type="text/javascript">' . "\n";
	$i = 0;
	
	foreach ($mapsEncontrados as $mapa)
	{	
		$varJS = "wmap_". $i . "_" . $mapa->map_id;
		echo " var $varJS =  wmaps_loadMap('wmap" . 
					   $mapa->map_id . "_canvas_" . $i . "', $mapa->map_latitud, $mapa->map_longitud, $mapa->map_zoom); \n";
		
		// **** Verifica si tiene puntos
		$points = $wpdb->get_results(" SELECT *  FROM " . $wpdb->prefix .  $wmaps_points_table .
									      " WHERE map_id = $mapa->map_id " );
	
		foreach ($points as $point)
		{
			echo "wmaps_set_point($varJS, $point->point_latitud, $point->point_longitud, " . 
								      "'$point->point_description' );\n";			
		};
		
		$i++;
	};
	echo "</script>\n";
}


add_filter("the_content","wmaps_setpost_maps");
add_action("wp_head","wmaps_insert_gmapskey");
add_action("wp_footer","wmaps_footer");


?>