<?php

function wmaps_maplist_page($IDmap)
	{
			
	  global $wpdb;
	  global $wmaps_maps_table;
	  global $wmaps_points_table;
	  
?>
<div class="wrap">

<script type="text/javascript">
	function borrar_mapa(idMapa, descMapa)
	{
		if (confirm("Are you sure you want to delete the map " +  descMapa + "?"))
		{
			document.forms["wmaps_listform"].wmap_maptodelete.value = idMapa;
			document.forms["wmaps_listform"].wmap_list_action.value = "delete";
			document.forms["wmaps_listform"].submit();
		}
	}
	
	function guardarkey()
	{
			
			document.forms["wmaps_listform"].wmap_list_action.value = "savegmapskey";
			document.forms["wmaps_listform"].submit();
		
	}
</script>
<?php 
	if ( $_POST["wmap_list_action"] == "delete" )
	{
		$wpdb->query("DELETE FROM " . $wpdb->prefix .  $wmaps_maps_table .
					 " WHERE map_id = " . $_POST["wmap_maptodelete"] );	
		
		$wpdb->query("DELETE FROM " . $wpdb->prefix .  $wmaps_points_table .
					 " WHERE map_id = " . $_POST["wmap_maptodelete"] );
	}
	
	if ($_POST["wmap_list_action"] == "savegmapskey")
	{
		update_option("wmaps_googlemaps_key", $_POST["wmaps_googlemapskey"]);
	}
	
?>
<h2>Wordpress Google Maps</h2>
<form name="wmaps_listform" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"
	  method="post" >
	<input type="hidden" name="wmap_maptodelete" value="">
	<input type="hidden" name="wmap_list_action" value="">
	<p class="submit">	  
		Before your start adding maps, you must obtain a Google Maps Key. 
		<a  href="http://code.google.com/apis/maps/signup.html" target="_TOP">You can get one here.</a><br>
	  	Enter your Google Maps Key 
	  	<input type="text" name="wmaps_googlemapskey" size="60" 
			   value="<?php echo get_option("wmaps_googlemaps_key"); ?>">
			
		<input type="button" value="Save Google Maps Key" 
			   onclick="guardarkey();">
	</p>				   
	<p class="submit">
		<input type="button" value="Add New Map" 
			   onclick="document.location='options-general.php?page=wmaps&wmaps_newmap=ok'">
		<br>
	</p>
</form>		
<br>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
	<th scope="col" class="manage-column column-name" style="">Map</th>
	<th scope="col" class="manage-column column-name" style="">For use it, include this text in your post</th>
	<th scope="col" class="manage-column column-email" style="">&nbsp;</th>
	<th scope="col" class="manage-column column-email" style="">&nbsp;</th>
</tr>
</thead>

<tbody id="users" class="list:user user-list">
<?php
	
	$query = " SELECT *  FROM " .
								   $wpdb->prefix . $wmaps_maps_table .
								 " ORDER BY map_description ";
	$mymaps = $wpdb->get_results($query);
	
	foreach ($mymaps as $themap)
	{
 ?>
        <tr id='user-1' class="alternate">
			<td class="username column-username">
				
				<?php echo $themap->map_description; ?>
				
		   </td>
		   <td>	
		   		<span class="stuffbox.inside">
		   			
				   		<input style="background: lightblue; text-align:center;" type="text" size="30" 
				   			   value='[getmymap  id="<?php echo $themap->map_id ?>"]'>
			   	    
		   	    </div>
		   </td>
		   <td class="username column-username">
				<a href="options-general.php?page=wmaps&wmaps_map_id=<?php echo $themap->map_id; ?>">Edit Map</a>
		   </td>
		   <td class="username column-username">
				<a href="javascript:borrar_mapa(<?php echo $themap->map_id ?>,'<?php echo $themap->map_description ?>');">Delete Map</a>
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