<!DOCTYPE html>
<html lang="en">
<head>
	
</head>
<body>

<div id="container">
	<h1>Welcome to the Weather!</h1>

	<div id="body">
		<p>There have been <?php echo $hourly; ?> hourly prediction recordings which is <?php echo $hourly/36; ?> pulls.</p>

		<p>There have been <?php echo $f10day; ?> ten day prediction recordings</p>
		
		<p>There have been <?php echo $current; ?> current weather recordings</p>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>