var Login = function () {


	return {

		//main function to initiate the module
		init: function () {

			jQuery('#forget-password').click(function () {
				jQuery('#loginform').css('display', 'none');
				jQuery('#forgotform').css('display', 'block');
			});

		}

	};

}();