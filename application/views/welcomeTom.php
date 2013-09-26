<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" 
      type="image/png" 
      href="http://weather.crabdance.com/weather/favicon.ico">
</head>
<body>

<div id="container">
	<h1>Welcome to the Weather!</h1>

	<div id="body">
		<p>There have been <?php echo $hourly; ?> hourly prediction recordings which is <?php echo round($hourly/36,0); ?> pulls.</p>

		<p>There have been <?php echo $f10day; ?> ten day prediction recordings</p>
		
		<p>There have been <?php echo $current; ?> current weather recordings</p>
		<p>The numbers in the first level of the array represent the prediction date </p>
		<p><pre> <?php print_r($prediction); ?> </pre></p>
	
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>