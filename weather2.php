<meta charset="UTF-8">
    <title>Local Weather App</title>
    <meta name="google" value="notranslate">
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet prefetch" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet prefetch" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css" type="text/css">
    <link rel="stylesheet prefetch" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons-wind.min.css" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<script src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="weather2_css.css">
    
  </head>
  <body>
    <div class="container">
      <div id="error"></div>
      <div id="city"></div>
      <div id="temperature"></div>
      <div id="results">
        <div id="condition"></div>
        <div id="wind-speed"></div>
      </div>
      <div>
        <input id="convert-button" type="button" class="btn btn-lg btn-primary" value="Display: °F / °C" />
      </div>
    </div>
    <footer class="footer">
      <div class="container-fluid text-center">
        <div class="footer-text text-muted row">Copyright &copy; <script>document.write(new Date().getFullYear());</script>. All rights reserved. Developed for the Dave and Dena Corporation </a></div>
        <div class="footer-logos row">
      
        </div>
      </div>
    </footer>
	
	
	<script>
	$(document).ready(function () {

  // OpenWeatherMap only works over HTTP. Check if using HTTPS
  // and present an error with a link to a HTTP version of the page.
  if (window.location.protocol != 'http:') {
    $('#condition').hide();
    $('#wind-speed').hide();
    $('#convert-button').hide();
    $('#error').html('This page is not supported over https yet.<br />' +
      'Please try again over http' 
     );
  } else {
    getLocation();

    // add a spinner icon to areas where data will be populated
    $('#condition').html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
    $('#wind-speed').html('<i class="fa fa-spinner fa-pulse fa-3x"></i>');
  }
});

function getLocation() {
  // Using the GEO IP API due to HTTP restrictions from OpenWeatherMap
  $.get('http://ip-api.com/json', function (loc) {
      $('#city').text(loc.city + ', ' + loc.region + ', ' + loc.country);
      getWeather(loc.lat, loc.lon, loc.countryCode);
    })
    .fail(function (err) {
      getWeather();
    });
}

function getWeather(lat, lon, countryCode) {
  var weatherAPI = 'http://api.openweathermap.org/data/2.5/weather?lat=' +
    lat + '&lon=' + lon + '&units=imperial' + '&type=accurate' +
    '&APPID=acf1740c353830d4274721fd70cddd08'; // please use your own App ID

  $.get(weatherAPI, function (weatherData) {
      // Also used by convert();
      temp = weatherData.main.temp.toFixed(0);
      tempC = ((temp - 32) * (5 / 9)).toFixed(0);

      var condition = weatherData.weather[0].description,
        id = weatherData.weather[0].id,
        speed = Number((weatherData.wind.speed * 0.86897624190816).toFixed(1)),
        deg = weatherData.wind.deg,
        windDir,
        iconClass,
        bgIndex,
        backgroundId = [299, 499, 599, 699, 799, 800],
        backgroundIcon = [
          'thunderstorm',
          'sprinkle',
          'rain',
          'snow',
          'fog',
          'night-clear',
          'cloudy',
        ],
        backgroundImg = [
          'http://tylermoeller.github.io/local-weather-app/assets/img/thunderstorm.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/sprinkle.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/rain.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/snow.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/fog.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/clear.jpg',
          'https://tylermoeller.github.io/local-weather-app/assets/img/cloudy.jpg',
        ];

      backgroundId.push(id);
      bgIndex = backgroundId.sort().indexOf(id);
      $('body').css('background-image', 'url(' + backgroundImg[bgIndex] + ')');
      iconClass = backgroundIcon[bgIndex];

    //Get wind compass direction. If API returns null, assume 0 degrees.
    if (deg) {
      var val = Math.floor((deg / 22.5) + 0.5),
          arr = [
            'N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW',
          ],
          windDir = arr[(val % 16)];
    } else {
      windDir = 'N';
    }

    //determine F or C based on country and add temperature to the page.
    var fahrenheit = ['US', 'BS', 'BZ', 'KY', 'PL'];
    if (fahrenheit.indexOf(countryCode) > -1) {
      $('#temperature').text(temp + '° F');
    } else {
      $('#temperature').text(tempC + '° C');
    }

    //write final weather conditions and wind information to the page
    $('#wind-speed').html(
      '<i class="wi wi-wind wi-from-' + windDir.toLowerCase() + '"></i><br>' +
      windDir + ' ' + speed + ' knots');
    $('#condition').html(
      '<i class="wi wi-' + iconClass + '"></i><br>' + condition);
  })
    .fail(function (err) {
    alert('There was an error retrieving your weather data. \n' +
          'Please try again later. Redirecting you to Seattle for now.');
    $('#city').text('Seattle, WA, United States');
    getWeather(47.6062, 122.3321, 'US');
  });
}

//toggle between celsius / fahrenheit
$('#convert-button').click(function () {
  if ($('#temperature').text().indexOf('F') > -1) {
    $('#temperature').text(tempC + '° C');
  } else {
    $('#temperature').text(temp + '° F');
  }

  this.blur(); // remove focus from the button
});
</script>
	
    
    
  </body>
</html>