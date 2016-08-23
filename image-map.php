<?php
/*
  Plugin Name: Image map plugin
  Plugin URI: http://www.evoxyz.com
  Description: short code for all image map and related members [image_map],date 15-7-2016
  Version: 1.0
  Author: Raman Kant Kamboj
  Author URI: http://google.co.in
 */
ob_start();
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if(isset($_GET['routid'])){
define( 'MY_PLUGIN_ROOT' , dirname(__FILE__) );
include_once( MY_PLUGIN_ROOT . '/jsonmapfile.php');
die;
}


function imagemap_form() {
	$url = site_url();
?>

<div id="map"  style="width: 600px; height: 400px"></div>

 <link rel="stylesheet" href="<?php echo plugins_url('css/leaflet.css', __FILE__); ?>">
<script src="<?php echo plugins_url('js/leaflet.js', __FILE__); ?>"></script>
<script src="<?php echo plugins_url('js/jquery.min.js', __FILE__); ?>"></script>
	
	<script>
	var markeyArray = [];
	var map;
	setInterval(function()  {
		
  var xhttp;
  
  if (window.XMLHttpRequest) {
    // code for modern browsers
    xhttp = new XMLHttpRequest();
    } else {
    // code for IE6, IE5
    xhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      //document.getElementById("demo").innerHTML = xhttp.responseText;
	 var planes = JSON.parse(xhttp.responseText);
     
	//    console.log(JSON.stringify(planes));
		console.log("markerArry length " + markeyArray.length);
	
	
	for(i = 0; i < planes.length; i++) {
		if((markeyArray.length == planes.length)) {
			console.log("UpDATINg marker " + i);
			markeyArray[i].setLatLng(L.latLng(planes[i].lat, planes[i].lng))
			markeyArray[i].update();
		} else {
			console.log("ADDING marker " + i);
			marker = new L.marker([planes[i].lat,planes[i].lng])
			.addTo(map)
				.bindPopup(planes[i].name)
				.openPopup();
			markeyArray.push(marker);
		}
	}

	
    }
  };
  xhttp.open("GET", "<?php echo $url; ?>/wp-admin/admin-ajax.php?routid=run", true);
  xhttp.send();
        }, 10000);
	
	
	map = L.map('map', {
			
      minZoom: 1,
      maxZoom: 4,
      center: [0, 0],
      zoom: 1,
      crs: L.CRS.Simple
	  
    });

    // dimensions of the image
    var w = 2000,
        h = 1500,
        url = '<?php echo plugins_url('images/plan-ground-1.png', __FILE__); ?>';

    // calculate the edges of the image, in coordinate space
    var southWest = map.unproject([0, h], map.getMaxZoom()-1);
    var northEast = map.unproject([w, 0], map.getMaxZoom()-1);
    var bounds = new L.LatLngBounds(southWest, northEast);

	
    L.imageOverlay(url, bounds).addTo(map);

    // tell leaflet that the map is exactly as big as the image
    map.setMaxBounds(bounds);

	
    </script>
	
<?php 
	}
// image_map a new shortcode: [image_map]
add_shortcode('image_map', 'imagemap_form');
?>