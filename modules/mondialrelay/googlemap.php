<script type="text/javascript" src="../../js/jquery/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">

var geocoder;
var map;
var infowindow = new google.maps.InfoWindow();
var markers = [];
var json_addresses = null;

function google_map_init() {
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(-34.397, 150.644);
	var myOptions = {
	  zoom: 11,
	  center: latlng,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map"), myOptions);
	
	geocoder.geocode( {'address': "<?php echo $_GET['address']; ?>"}, function(results, status)
	{
	  if (status == google.maps.GeocoderStatus.OK)
	  {
		map.setCenter(results[0].geometry.location);
		var marker = new google.maps.Marker({
			map: map, 
			position: results[0].geometry.location
		});
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent("<?php echo $_GET['address']; ?>");
			infowindow.open(map,marker);
		});
	  }
	});
}

function codeAddress(address, address_google)
{
	geocoder.geocode( {'address': address_google}, function(results, status)
	{
 		 if (status == google.maps.GeocoderStatus.OK)
		 {
		 	var image = new google.maps.MarkerImage('<?php echo $_GET['relativ_base_dir']; ?>modules/mondialrelay/kit_mondialrelay/marker.gif');
			var marker = new google.maps.Marker({
				map: map, 
				position: results[0].geometry.location,
				icon : image
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent('<img src="<?php echo $_GET['relativ_base_dir']; ?>modules/mondialrelay/kit_mondialrelay/MR_small.gif" />' + ' ' + address);
				infowindow.open(map,marker);
			});
		}
	});
			
}

function recherche_MR(args)
{
	var ok = 1;
	if (ok == 1)
	{
		$.ajax({
			type: "POST",
			url: 'kit_mondialrelay/RecherchePointRelais_ajax.php',
			data: args ,
			dataType: 'json',
			async : false,
			success: function(obj)
			{
					json_addresses = obj;
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	}
	else
	{
		alert('Formulaire incomplet');
		return false;
	}
}

</script>

<div id="map" style="height:300px; width:500px; border:1px;" ></div>

<?php echo '<script type="text/javascript">

	recherche_MR(\'relativ_base_dir='.$_GET['relativ_base_dir'].'&Pays='.$_GET['Pays'].'&Ville='.$_GET['Ville'].'&CP='.$_GET['CP'].'&Taille=&Poids='.$_GET['Poids'].'&Action='.$_GET['Action'].'&num='.$_GET['num'].'\');

	window.onload = function()
		{
			var cpt = 0;
			google_map_init();
			if (json_addresses && json_addresses.addresses)
				while (json_addresses.addresses[cpt])
				{
					if (json_addresses.addresses[cpt].address3.length)
					{
						address_google = json_addresses.addresses[cpt].address3+\' \'+json_addresses.addresses[cpt].postcode+\' \'+json_addresses.addresses[cpt].city+\' \'+json_addresses.addresses[cpt].iso_country;
						address = json_addresses.addresses[cpt].address1+\'<br />\'+json_addresses.addresses[cpt].address2+\' \'+json_addresses.addresses[cpt].address3+\'<br />\'+json_addresses.addresses[cpt].postcode+\' \'+json_addresses.addresses[cpt].city+\' \'+json_addresses.addresses[cpt].iso_country;
						codeAddress(address, address_google);
					}
						cpt++;
				}
		}
</script>';
?>
