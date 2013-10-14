<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Validation test</title>
</head>
<body>
	<div id="form">
		<form action="receiver.php" method="post" enctype="multipart/form-data">
			<div><input type="file" name="file" /></div>
			<div><input type="text" name="title" placeholder="title"/></div>
			<div><input type="text" name="category" placeholder="category"/></div>
			<div><input type="submit" value="send!" /></div>
		</form>
	</div>
</body>
</html>