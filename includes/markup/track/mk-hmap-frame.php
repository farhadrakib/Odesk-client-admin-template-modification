<!doctype html>
<html lang="en">
<head><title></title></head>
<body style="padding: 0; margin: 0;">
<iframe src="<?php echo str_replace("~", ".", urldecode($_GET['url'])) ?>"
        style="position: relative; z-index: 0; width: 100%; height: <?php echo $_GET['height'] ?>px;" id="spy-iframe-lvl2" scrolling="no"
        frameborder="0" noresize="noresize"></iframe>
</body>
</html>