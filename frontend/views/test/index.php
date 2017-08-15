<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>

<style type="text/css">
	div{
		width: 50px;
		height: 50px;
		background: red;
	}
</style>
</head>
<body>
	<div id="butt">点我</div>
</body>
<script type="text/javascript">
	var btn = document.getElementById('butt');
	btn.onclick = function () {
		location.href = '/';
	}
</script>
</html>