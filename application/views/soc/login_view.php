<html>
<head>

</head>
<body>
<div id = "login">
<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("soc/homepage");?>

<p>
    <label for = "identity">Username: </label>
    <?php echo form_input($identity);?>
</p>

<p>
    <label for="password">Password: </label>
    <?php echo form_input($password);?>
</p>

<p>
    <label for="remember">Remember Login? </label>
    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
</p>

<p><?php echo form_submit('submit', "Login");?></p>

<?php echo form_close();?>

</div>
</body>
</html>