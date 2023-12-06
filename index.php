<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaflet Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <style>
    #map {
      width: 100%;
      height: 91.6vh;
    }

    .footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      background-color: #da649c;
      color: white;
      text-align: center;
    }
    #distance-text{
      font-size: 30px;
    }
  </style>
</head>
<body>

<div id="map"></div>
<div id="distance-container" class="footer">
  <p id="distance-text" style="color: white;"></p>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
var map = L.map('map').setView([0, 0], 18);
var distanceContainer = document.getElementById('distance-container');
var distanceText = document.getElementById('distance-text');

var userLocationIcon = L.icon({
  iconUrl: 'logo.png',
  iconSize: [32, 32],
  iconAnchor: [16, 32],
  popupAnchor: [0, -32]
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
}).addTo(map);

if (!navigator.geolocation) {
  console.log("Your browser doesn't support geolocation feature!");
} else {
  navigator.geolocation.watchPosition(getPosition);
}

var userLocationMarker;

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    function (position) {
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
      var userLocation = L.latLng(latitude, longitude);

      if (userLocationMarker) {
        map.removeLayer(userLocationMarker);
      }

      userLocationMarker = L.marker(userLocation).addTo(map);
      var waypointIcon = L.icon({
        iconUrl: 'logo.png',
        iconSize: [45, 45],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      var waypointIcon2 = L.icon({
        iconUrl: 'location.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      var waypointMarker = L.marker(L.latLng(14.021276, 120.653938), {icon: waypointIcon}).addTo(map);

      // Initial call to updateDistance
      updateDistance(userLocation, waypointMarker.getLatLng());

      var control = L.Routing.control({
        waypoints: [
          userLocation,
          L.latLng(14.021276, 120.653938),
        ],
        routeWhileDragging: false,
        createMarker: function (i, waypoint, n) {
          var markerOptions = {
            draggable: true,
            icon: null,
          };

          if (i === 0) {
            markerOptions.icon = waypointIcon2;
          } else if (i === n - 1) {
            markerOptions.icon = waypointIcon;
          }

          var marker = L.marker(waypoint.latLng, markerOptions);

          marker.on('move', function (e) {
            // Update distance when the marker is moved
            updateDistance(userLocationMarker.getLatLng(), e.target.getLatLng());
          });

          return marker;
        }
      }).addTo(map);

      control.on('routingerror', function (e) {
        alert('Error: ' + e.error.message);
      });

      var bounds = L.latLngBounds([userLocation, L.latLng(14.021276, 120.653938)]);
      map.fitBounds(bounds);
    },
    function (error) {
      alert('Error getting the user\'s location: ' + error.message);
    }
  );
} else {
  alert('Geolocation is not supported by this browser.');
}

var marker, circle;

function getPosition(position) {
  console.log(position);
  var lat = position.coords.latitude;
  var long = position.coords.longitude;
  var accuracy = position.coords.accuracy;
  if (marker) {
    map.removeLayer(marker);
  }
  if (circle) {
    map.removeLayer(circle);
  }
  marker = L.marker([lat, long]);
  circle = L.circle([lat, long], {radius: accuracy});
  var featureGroup = L.featureGroup([marker, circle]).addTo(map);
  map.fitBounds(featureGroup.getBounds());
  console.log("Your coordinate is: Lat: " + lat + " Long: " + long + " Accuracy: " + accuracy);
}

function updateDistance(userLocation, waypoint) {
  var distance = userLocation.distanceTo(waypoint);
  distanceText.innerHTML = 'Distance: ' + distance.toFixed(2) + ' meters';

  console.log('location mo :',distance);
}

</script>

</body>
</html>
