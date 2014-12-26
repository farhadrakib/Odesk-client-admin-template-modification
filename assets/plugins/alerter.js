function alerter(text, type) {
	var cls = ""
	switch (type) {
		case 1:
			cls = "alert-error";
			break;
		case 2:
			cls = "alert-success";
			break;
		case 3:
			cls = "alert-info";
			break;
	}

	setTimeout(function () {
		jQuery("iframe", top.document).height(jQuery(document).height())
	}, 100);
	return jQuery('<div class="alert alert-block ' + cls + ' fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>' + text + '</div>');
}