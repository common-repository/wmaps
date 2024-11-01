<?php
// Start XML file, echo parent node
global $wpdb;
global $wmaps_points_table;

$data = "<points>";

// ******************************************
// ******* CARGA LOS PUNTOS EN EL MAPA
// ****************************************** 

 
	$points = $wpdb->get_results(" SELECT *  FROM " .
								 	$wpdb->prefix .  $wmaps_points_table .
								  " WHERE map_id =  " . $_GET["MAP_ID"] );

// ******************************************
// ******* CREA EL XML
// ******************************************

foreach ($points as $point)
{	
		$data .= "<point ";
		$data .= 'description="' . $point->point_description . '" ';
		$data .= 'latitud="'     . $point->point_latitud 	 . '" '; 
		$data .= 'longitud="' 	 . $point->point_longitud 	 . '" ';
		$data .= "/>\n";
	   
};

// End XML file
$data .= "</points>";
echo $data;
?>