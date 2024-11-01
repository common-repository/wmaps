<?php


// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Options:
    if ($_REQUEST["wmaps_map_id"] || $_REQUEST["wmaps_newmap"] )
    	add_options_page('My Maps', 'My Maps', 8, 'wmaps', 'mt_options_page');
    else
    	add_options_page('My Maps', 'My Maps', 8, 'wmaps', 'wmaps_maplist_page');
    
}


// mt_options_page() displays the page content for the Test Options submenu
function mt_options_page() {
	global $wpdb;
	global $wmaps_maps_table;
	global $wmaps_points_table;
?>
<!-- **** Google Maps **** -->
<script  src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo get_option("wmaps_googlemaps_key") ?>"
		 type="text/javascript">
</script>

<?php
  
	// *** Datos Mapa
	$mapID = $_REQUEST["wmaps_map_id"];
	$mapDescription = "Enter here a short description";
	$mapLongitud    = "-104";
	$mapLatitud     = "20";
	$mapZoom 		= "2";
	$mapWidth		= "150";
	$mapHeight		= "150";
	$mapTxtAlignNone  = "checked";
	$mapTxtAlignLeft  = "";
	$mapTxtAlignRight = "";
	   
	// *** Datos Punto Nuevo
	$pointDescription = "Enter a point description";
	$pointLongitud    = "0";
	$pointLatitud     = "0";
			
	// *******  GUARDAR MAPA ********
	if ( !($_POST["wmaps_submit"] == "ok") )
	{
		if ($mapID)
		{
			$mapaInfo = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . $wmaps_maps_table .  
							 			   " WHERE map_id = $mapID ");
			
			$mapDescription = $mapaInfo[0]->map_description;
			$mapLongitud    = $mapaInfo[0]->map_longitud;
			$mapLatitud     = $mapaInfo[0]->map_latitud;
			$mapZoom 		= $mapaInfo[0]->map_zoom;
			$mapWidth		= $mapaInfo[0]->map_width;
			$mapHeight		= $mapaInfo[0]->map_height;
			
		   $mapTxtAlignNone = "";
		   switch(trim(strtolower($mapaInfo[0]->text_align)))
		   {
		   		case "left": 
		   				$mapTxtAlignLeft = "checked";
		   				break;
		   				
		   		case "right":
		   				$mapTxtAlignRight = "checked";
		   				break;
		   		default:
		   				$mapTxtAlignNone = "checked";
		   				break; 
		   }
		}
	}
	else
	{
		   // *** Datos Mapa
		   $mapDescription = str_replace("\'"," ",$_POST["wmaps_map_description"]);
		   $mapLongitud    = $_POST["wmaps_map_longitud"];
		   $mapLatitud     = $_POST["wmaps_map_latitud"];
		   $mapZoom 	   = $_POST["wmaps_map_zoom"];
		   $mapWidth	   = $_POST["wmaps_map_width"];
		   $mapHeight	   = $_POST["wmaps_map_height"];
		   
		   $mapTxtAlignNone = "";
		   $mapAlignValue = trim(strtolower($_POST["wmaps_map_align"]));
		   switch($mapAlignValue)
		   {
		   		case "left": 
		   				$mapTxtAlignLeft = "checked";
		   				break;
		   				
		   		case "right":
		   				$mapTxtAlignRight = "checked";
		   				break;
		   		default:
		   				$mapTxtAlignNone = "checked";
		   				break; 
		   }
		   
		    if ( is_numeric($_POST["wmaps_point_todelete"]) )
		    {	$query = "DELETE FROM " . $wpdb->prefix . $wmaps_points_table .
		    			      " WHERE id_point = " . $_POST["wmaps_point_todelete"];
		    	
		    	$wpdb->query($query);
		    	
		    }
		    else
		    if ($mapID)
			    {
			    	 $query = " UPDATE " . $wpdb->prefix . $wmaps_maps_table .  
				   				  " SET map_description = '$mapDescription', " . 
				   				      " map_longitud = $mapLongitud, " . 
				   				      " map_latitud  = $mapLatitud,  "  . 
				   				      " map_zoom     = $mapZoom, " . 
				   				      " map_width    = $mapWidth, " .
			    	 			      " map_height   = $mapHeight,  " . 
			    	 				  " text_align   = '$mapAlignValue' " .
			    	 		 " WHERE map_id = $mapID ";
			    	 
			    	$wpdb->query($query);
			    }
			    else
			    {
				    $query = "INSERT INTO " . $wpdb->prefix . $wmaps_maps_table .  
				   					 "( map_description, map_longitud, map_latitud, map_zoom, " . 
				   					   "map_width, map_height, text_align ) " . 
				   				"VALUES ('$mapDescription', $mapLongitud, $mapLatitud, $mapZoom, " . 
				   				         " $mapWidth, $mapHeight, '$mapAlignValue') ";
			    
			     
			   		 $wpdb->query($query);
			    	 $lastID = $wpdb->get_results("SELECT MAX(map_id) as last_map_id " .
			    		  						   " FROM " . $wpdb->prefix . $wmaps_maps_table .
			    							 	  " WHERE map_description = '$mapDescription'");
			    	 $mapID = $lastID[0]->last_map_id;
			    }
			    
				// ******* Se está agregando un punto nuevo *******
				if ($_POST["wmaps_point_latitud"] != "")
				{
				   $pointDescription  = str_replace("\'","",$_POST["wmaps_point_description"]);
				   $pointLongitud     = $_POST["wmaps_point_longitud"];
				   $pointLatitud      = $_POST["wmaps_point_latitud"];
				   
				    
				    $query = "INSERT INTO " . $wpdb->prefix . $wmaps_points_table .  
			   					 " ( map_id, point_description, point_longitud, point_latitud ) " . 
			   				"VALUES ($mapID, '$pointDescription', $pointLongitud, $pointLatitud ) "; 
				    $wpdb->query($query);
				    
				};
	}
	 

?>

<form name="wmaps_form" method="post" onsubmit="return validarDatos(this);" 
	  action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	  
<div class="wrap">
<?php
    // Now display the options editing screen

    // header
	if ($mapID)
    	echo "<h2>" . __( 'Edit Map', 'mt_trans_domain' ) . "</h2>";
    else
       	echo "<h2>" . __( 'Add New Map', 'mt_trans_domain' ) . "</h2>";
    
    // options form
    
 ?>
   <?php if ( is_numeric($_POST["wmaps_point_todelete"]) ) { ?>
    <div class="updated"><p><strong><?php _e('The point was deleted.', 'mt_trans_domain' ); ?></strong></p></div><br>	
    <? }else if ( $_POST["wmaps_submit"] == "ok" ) { ?>
    <div class="updated"><p><strong><?php _e('Map information saved.', 'mt_trans_domain' ); ?></strong></p></div><br>	
    <? }; ?>

 	<!-- ***** Map Options ***** -->
 	<div class="stuffbox">
 		<p class="inside">
 			<?php if ($mapID) { ?>
 			<b>How to use this map in your post</b><br>
 			Copy and paste this text keywords in any part of your post:<br>
 			<input style="background: lightblue; text-align:center;" type="text" size="30" value='[getmymap  id="<?php echo $mapID ?>"]'>
 			<?php } else { ?>
 				<b>After you save your map, it will show the instructions how to use it in your posts</b>
 			<?php } ?>
 		</p>
 	</div>
 	<span id="mapdescription" class="stuffbox" >
 		
		 <label for="wmaps_map_description">Map Description</label>
		 <span class="inside">	
		 	<input type="text" size="45" maxlength="40" id="wmaps_map_description" name="wmaps_map_description"
		 		   value="<?php echo $mapDescription ?>"> 
	    
 
	    <input type="hidden" id="wmaps_map_latitud"  name="wmaps_map_latitud"  value="<?php echo $mapLatitud ?>">
 		<input type="hidden" id="wmaps_map_longitud" name="wmaps_map_longitud" value="<?php echo $mapLongitud ?>">
 		<input type="hidden" id="wmaps_map_zoom"     name="wmaps_map_zoom"     value="<?php echo $mapZoom ?>">
 		<input type="hidden" id="wmaps_map_id"       name="wmaps_map_id"       value="<?php echo $mapID ?>">
 		</span>
 	</span>
 	<span class="stuffbox" style="margin-right:10px;">
 		<span class="inside">
	 		<label for="wmaps_map_width">Width</label>
		 	<input type="text" size="5" maxlength="3" id="wmaps_map_width" name="wmaps_map_width"
		 		   value="<?php echo $mapWidth ?>">px
		</span>
		
 	</span>
 	<span class="stuffbox" style="margin-right:10px;">
 		<span class="inside">
	 		<label for="wmaps_map_height">Height</label>
		 	<input type="text" size="5" maxlength="3" id="wmaps_map_height" name="wmaps_map_height"
		 		   value="<?php echo $mapHeight ?>">px
		</span><br/><br/>
		<span>Post's text alignmen: <input type="radio" name="wmaps_map_align" value="none" <?php echo $mapTxtAlignNone ?>>None </span>
		<span><input type="radio" name="wmaps_map_align" value="left" <?php echo $mapTxtAlignLeft ?>>Left</span>
		<span><input type="radio" name="wmaps_map_align" value="right" <?php echo $mapTxtAlignRight ?>>Right</span>
 	</span>
 
 	<!-- ***** Point Options ***** -->
    <span id="wmaps_newpoint" class="stuffbox">
    	<div id="wmaps_addpoint_button">
	    	<p class="submit">
				<input type="button" name="AddPoint" value="Clik here to add new point to the map"
					   onclick="wmaps_add_point();" />
			</p>
		</div><br>
		<div id="wmaps_newpoint_fields" class="inside" style="visibility:hidden; display: none;">
		
	    	<label for="wmaps_point_description">Point Description</label>
	 		<input type="text" size="45" maxlength="40" id="wmaps_point_description" name="wmaps_point_description"
	 		   value="<?php echo $pointDescription ?>">	
	 		<input type="hidden" id="wmaps_point_latitud" name="wmaps_point_latitud" value="">
	 		<input type="hidden" id="wmaps_point_longitud" name="wmaps_point_longitud" value="">
	 		<BR>
	 		<p>Drag the red point  over the map to set the localization of the point.<br>
	 		   Also, you can make click over any area on the map.
	 		</p>
 		</div>	
    </span>
    
  <div id="wmaps_map_canvas" 
 	  style="border: 1px solid black; width: <?php echo $mapWidth ?>px; height: <?php echo $mapHeight ?>px; margin-right:10px;">
 </div>
 </p>


<p class="submit">
	<input type="hidden" name="wmaps_submit" value="ok">
	<input type="hidden" name="wmaps_point_todelete" value="">
	<input type="submit" name="Submit" value="<?php _e('Save Map Information', 'mt_trans_domain' ) ?>" />&nbsp;
	<input type="button" name="Return" value="<?php _e('Return to map list', 'mt_trans_domain' ) ?>"
		   onclick="document.location='options-general.php?page=wmaps' " />
</p>

</form>

<?php 
	if ($mapID)
	 getTableMarkers($mapID); 
?>
</div> <!-- **** DIV WRAPPER *** -->


<script type="text/javascript" src="<?php echo get_option("siteurl") ?>/wp-content/plugins/wonkas_maps/wmaps.js">
</script>

<script type="text/javascript">
	var wmapLocGMap;
	if (GBrowserIsCompatible()) 
	{
	    wmapLocGMap = new GMap2(document.getElementById("wmaps_map_canvas"));
		wmapLocGMap.addControl(new GSmallMapControl());
		mapCenter = new GLatLng(<?echo $mapLatitud ?>, <?echo $mapLongitud ?>);
		wmapLocGMap.setCenter(mapCenter, <?echo $mapZoom ?>);
		
		GEvent.addListener(wmapLocGMap, "moveend", function(){
				
			
	  	});
	}
	<?php
		if ($mapID)
		{
			$points = $wpdb->get_results(" SELECT *  FROM " .
									 			$wpdb->prefix .  $wmaps_points_table .
									      " WHERE map_id = $mapID " );
	
			foreach ($points as $point)
			{
				echo "wmaps_set_point(wmapLocGMap,$point->point_latitud, $point->point_longitud, " . 
								      "'$point->point_description' );\n";			
			};
		}	
	?>
</script>

<?php
	 
}


 

function getTableMarkers($IDmap)
	{
			
	  global $wpdb;
	  global $wmaps_points_table;
?>
		
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
	<th scope="col" id="name" class="manage-column column-name" style="">Point</th>
	<th scope="col" id="email" class="manage-column column-email" style="">Delete ?</th>
</tr>
</thead>

<tbody id="users" class="list:user user-list">
<?php
	
	
	$points = $wpdb->get_results(" SELECT *  FROM " .
								   $wpdb->prefix .  $wmaps_points_table .
								 " WHERE map_id = $IDmap " );
	
	foreach ($points as $point)
	{
 ?>
        <tr id='user-1' class="alternate">
			<td class="username column-username">
				<a href="javascript:goto(<?php echo$point->point_latitud?>,<?php echo $point->point_longitud ?>)">
				<img src="http://map.spieslike.us/i/marker_blue.png" border="0" align="middle">
				<?php echo $point->point_description; ?>
				</a>
		   </td>
		   <td class="username column-username">
				<a href="javascript:borrar_punto('<?php echo $point->id_point; ?>','<?php echo $point->point_description; ?>')">
						Delete this point</a>
		   </td>	
		</tr>
        
        <?
    }

?>
	
    </tbody>
</table>
<?php 
	}

?>