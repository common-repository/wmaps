
/*

function CDownloadUrl(method, url, func) {
   var httpObj;
   var browser = navigator.appName;
   if(browser.indexOf("Microsoft") > -1)
      httpObj = new ActiveXObject("Microsoft.XMLHTTP");
   else
      httpObj = new XMLHttpRequest();
 
   httpObj.open(method, url, true);
   httpObj.onreadystatechange = function() {
      if(httpObj.readyState == 4){
         if (httpObj.status == 200) {
            var contenttype = httpObj.getResponseHeader('Content-Type');
            if (contenttype.indexOf('xml')>-1) {
               func(httpObj.responseXML);
            } else {
               func(httpObj.responseText);
            }
         } else {
            func('Error: '+httpObj.status);
         }
      }
   };
   httpObj.send(null);

}

function getpoints(mapID,map)
{
	CDownloadUrl("get","http://localhost/wordpress/wp-content/plugins/wonkas_maps/wmaps_getpoints.php?MAP_ID=" +  mapID, function(data) 
	{
       
       var xml = GXml.parse(data);
       var markers = xml.documentElement.getElementsByTagName("point");
       
       for (var i = 0; i < markers.length; i++) 
       {	
       		// Datos
       		var description = markers[i].getAttribute("description");
            var point = new GLatLng(parseFloat(markers[i].getAttribute("latitud")),
                                    parseFloat(markers[i].getAttribute("longitud")));
            
            // **** Agrega el marker al mapa
            var marker = new GMarker(point, {draggable: false});
            map.addOverlay(marker);
       }
	           
     });

}

*/


function borrar_punto(pointID,description)
{
	if (confirm("Are you sure you want to delete the point " + description ))
	{
		document.forms["wmaps_form"].wmaps_point_todelete.value = pointID;
		document.forms["wmaps_form"].submit();
	}
}

function goto(lat,lng)
{	
	wmapLocGMap.setCenter(new GLatLng(lat,lng));
}

// **** Carga el mapa en el blog ****
function wmaps_loadMap(strID, mapLat, mapLng, mapZoom)
{
	if (GBrowserIsCompatible()) 
	{
	    var wmapLocGMap = new GMap2(document.getElementById(strID));
		wmapLocGMap.addControl(new GSmallMapControl());
		mapCenter = new GLatLng(mapLat, mapLng);
		wmapLocGMap.setCenter(mapCenter, mapZoom);
		
		return wmapLocGMap;
	}
}

function wmaps_set_point(map, lat, lng, textInfo)
{		
	  // **** Agrega el marker
	  var markerIcon = new GIcon(G_DEFAULT_ICON);
	  markerIcon.image = "http://map.spieslike.us/i/marker_blue.png";
	  markerIcon.iconSize = new GSize(20, 34);
	  markerIcon.iconAnchor = new GPoint(12,12);
	  
	  var marker = new GMarker(new GLatLng(lat,lng), {icon:markerIcon,  draggable: false});
	  
	  
	  GEvent.addListener(marker, "click", function() {
            marker.openInfoWindowHtml(textInfo);
      });
          	                             
	  map.addOverlay(marker);
}

function validarDatos(forma)
{
	if (forma.wmaps_map_description.value == "")
	{
		alert("You must type a description for your map");
		forma.wmaps_map_description.focus();
		return false;
	}
	
	if (isNaN(forma.wmaps_map_width.value) || (!forma.wmaps_map_width.value))
	{
		alert("The width of the map must be a number");
		forma.wmaps_map_width.focus();
		return false;
	}
	
	
	if (isNaN(forma.wmaps_map_height.value) || (!forma.wmaps_map_height.value))
	{
		alert("The width of the map must be a number");
		forma.wmaps_map_height.focus();
		return false;
	}
	
	asignarDatosMapa(forma);
	return true;
}
function asignarDatosMapa(forma) {
	var centroMapa = wmapLocGMap.getCenter();
	
	forma.wmaps_map_latitud.value  = centroMapa.lat();
	forma.wmaps_map_longitud.value = centroMapa.lng();
	forma.wmaps_map_zoom.value     = wmapLocGMap.getZoom();
	
}

function wmaps_add_point()
{		
	  // **** Agrega el marker
	  var markerIcon = new GIcon(G_DEFAULT_ICON);
	  markerIcon.iconSize = new GSize(25, 25);
	  markerIcon.iconAnchor = new GPoint(12,12);
	  
	  center = wmapLocGMap.getCenter();
	  marker = new GMarker(center, {draggable: true});
	  wmapLocGMap.addOverlay(marker);
	  
	  document.forms["wmaps_form"].wmaps_point_latitud.value  = marker.getPoint().lat();
	  document.forms["wmaps_form"].wmaps_point_longitud.value = marker.getPoint().lng();
	  
	  
	  
	  // **** Evento Onclick que asigna coordenadas
	  GEvent.addListener(marker, "dragstart", function() {wmapLocGMap.closeInfoWindow();
	  });

	  GEvent.addListener(marker, "dragend", function() {//marker.openInfoWindowHtml(infoWindow,"");
		  document.forms["wmaps_form"].wmaps_point_latitud.value  = marker.getPoint().lat();
		  document.forms["wmaps_form"].wmaps_point_longitud.value = marker.getPoint().lng();
				  
		  
	  });
	  
	  GEvent.addListener(wmapLocGMap, "click", function (overlay,point){
					if (point){
							var forma =  document.forms["wmaps_form"];
							
							marker.setPoint(point);
							forma.wmaps_point_longitud.value = point.x;
							forma.wmaps_point_latitud.value  = point.y;
							
					}
	  });
	  
	  wmaps_showdiv("wmaps_newpoint_fields");
	  wmaps_hidediv("wmaps_addpoint_button");
}

function wmaps_showdiv(divID)
{
	var div = document.getElementById(divID);
	div.style.visibility = "visible";
	div.style.display = "block";
}

function wmaps_hidediv(divID)
{
	var div = document.getElementById(divID);
	div.style.visibility = "hidden";
	div.style.display = "none";
}

