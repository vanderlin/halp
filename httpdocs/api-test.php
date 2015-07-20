<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<title>API Demo</title>
</head>
<body>

<form accept="/api-test.php" method="POST">
	<input type="text" name="username" placeholder="username" value="<?php echo $_POST['username'] ?>">
	<input type="password" name="password" placeholder="password" value="<?php echo $_POST['password'] ?>">
	<input type="text" name="access_token" placeholder="access_token" value="<?php echo $_POST['access_token'] ?>">
	<button type="submit">Submit</button>
</form>

<div class="response">
	<pre><code></code></pre>
</div>
<?php if (isset($_POST['username'])): ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$.ajax({
			type: "GET",
		  	url: "http://localhost:8888/api",
		  	dataType: 'json',
		  	async: false,
		  	username: "<?php echo $_POST['username'] ?>",
		 	password: "<?php echo $_POST['password'] ?>",
			success: function (e) {
				console.log(e);
				$('.response code').html(JSON.stringify(e));
		  	}
		});
	});
</script>

<?php endif ?>

</body>
</html>