<?php
$user_ip = $_SERVER['REMOTE_ADDR'];
$lookup = json_decode(file_get_contents("https://ipinfo.io/json"), true);
$coords = explode(",", $lookup['loc'] ?? "0,0");
$lat = $coords[0];
$lon = $coords[1];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Map Viewer</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            margin: 0;
            padding: 30px;
            background: #111;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            margin-bottom: 8px;
            font-size: 30px;
        }
        p {
            color: #e6e6e6;
            font-size: 15px;
            margin-bottom: 18px;
        }
        #mapArea {
            width: 90%;
            height: 500px;
            border-radius: 14px;
            border: 2px solid #333;
            box-shadow: 0 0 18px rgba(0,0,0,0.6);
            overflow: hidden;
        }
        .leaflet-control-zoom-in,
        .leaflet-control-zoom-out {
            background: #1c1c1c !important;
            color: white !important;
        }
        .leaflet-popup-content-wrapper {
            background: #1c1c1c !important;
            color: white !important;
        }
        .leaflet-popup-tip {
            background: #1c1c1c !important;
        }
    </style>
</head>
<body>

<h2>📍 Map Location</h2>
<p>Your IP address: <b><?php echo $user_ip; ?></b></p>

<div id="mapArea"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var map = L.map('mapArea').setView([<?php echo $lat; ?>, <?php echo $lon; ?>], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

var serverPin = L.marker([<?php echo $lat; ?>, <?php echo $lon; ?>]).addTo(map);
serverPin.bindPopup("📡 Server Position");

var blueDot = L.icon({
    iconUrl: "https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png",
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -16]
});

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        var uLat = pos.coords.latitude;
        var uLon = pos.coords.longitude;

        var userPin = L.marker([uLat, uLon], { icon: blueDot }).addTo(map);
        userPin.bindPopup("💙 Your Real Location");

        var both = L.featureGroup([serverPin, userPin]);
        map.fitBounds(both.getBounds().pad(0.25));
    });
}
</script>

</body>
</html>
