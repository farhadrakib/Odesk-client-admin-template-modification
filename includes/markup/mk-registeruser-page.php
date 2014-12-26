<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker 
 * http://HeatMapTracker.com
 */
?>
<?php if (!defined('HMT_STARTED') || !isset($this->PLUGIN_PATH))
    die('Can`t be called directly');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0 "/>
        <title><?php echo $this->OPTIONS['brandname'] ?></title>
        <link media="all" rel="stylesheet" type="text/css" href="<?php echo $this->PLUGIN_URL ?>css/all.css"/>
        <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'/>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body class="login">
        <div id="wrapper">
            <div id="header">
                <strong class="logo add">
	                <a href="<?php echo $this->PLUGIN_URL ?>">
		                <img src="<?php echo $this->getBrandLogo(); ?>" alt="logo"/>
	                </a>
                </strong>
            </div>
            <div id="main">
                <div class="login-box">
                    <div class="holder">

                        <?php
                        require_once($this->FUNCTIONS_PATH . 'fn-registeruser.php');

                        $package_id = $_GET['package'];
                        $package = $this->PACKAGES[$package_id];
                        if (!$package) {
                            ?>
                            <p>Sorry, this subscription is closed.</p>
                            <script type="text/javascript">
                                setTimeout(function () {
                                    window.location.href = '<?php echo admin_url() ?>?login';
                                }, 5000);
                            </script>
                            <?php
                            die();
                        }
                        list($current_step, $prev_step) = detect_register_step();
                        $is_postback = $prev_step == $current_step;

                        $errors = array();
                        if ($current_step == 'start') {
                            //Have not submitted the registration form yet
                            if ($is_postback) {
                                $errors = validate_start_form($this);
                            }

                            if (!$is_postback || !empty($errors)) {
                                //First form not submitted or returned errors
                                include($this->MARKUP_PATH . '/forms/frm-register-user.php');
                            } else {
                                //First form submitted and processed with no errors
                                $user_key = $_POST['user_key']; //we set it on validation
                                $prev_step = 'choose_plan';
                                include($this->MARKUP_PATH . '/forms/frm-choose-plan.php');
                            }
                        } else if ($current_step == 'choose_plan') {

	                        $user_key = $_POST['user_key']; //we get it from client post as hidden
	                        $user = get_user_by('user_key', $user_key);

	                        if (!$user)
                                $errors[] = '<strong>Plan Error</strong>. User does not exist.';

                            if (empty($errors) && !$is_postback) { //postback
                                $errors = validate_choose_plan($user);
                            }

                            if ($is_postback && !empty($errors))
                                include($this->MARKUP_PATH . '/forms/frm-choose-+.php');
                            else {
	                            if(isset($_POST['free_package'])) {
		                            if (!tables_created($user->user_key)) {
			                            header('HTTP/1.1 500 Internal Server Error');
		                            }
		                            create_user_tables( $_POST['user_key'] );
		                            include($this->MARKUP_PATH . '/mk-thankyou-page-free.php');
	                            } else {
		                            $order_total = $_POST['order_total'];
		                            include( $this->MARKUP_PATH . '/forms/frm-submit-plan.php' ); //flies to paypal
	                            }
                            }
                        } else {
                            die('Unknown registration step: ' . $current_step);
                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>
        <!-- END COPYRIGHT -->
        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE PLUGINS -->
        <!-- script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script -->
        <!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js"
        type="text/javascript"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
        <!--[if lt IE 9]>
        <script src="assets/plugins/excanvas.js"></script>
        <script src="assets/plugins/respond.js"></script>
        <![endif]-->
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/jquery-cookie.js" type="text/javascript"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/plugins/alerter.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/app.js"></script>
        <script src="<?php echo $this->PLUGIN_URL ?>assets/scripts/login.js"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <script>
                            jQuery(document).ready(function () {
                                // initiate layout and plugins
                                App.init();
                                Login.init();

                                //first step submitter
                                jQuery(".fldsubmitLicense").click(function () {

                                    jQuery("#regform .alert").remove();
                                    //validate form
                                    if (jQuery('#bname').val() == "") {
                                        jQuery('#bname').focus();
                                        return false;
                                    }
                                    if (jQuery('#site').val() == "") {
                                        jQuery('#site').focus();
                                        return false;
                                    }
                                    if (jQuery('#email').val() == "") {
                                        jQuery('#email').focus();
                                        return false;
                                    } else {
                                        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                                        if (!emailReg.test(jQuery('#email').val())) {
                                            jQuery('#email').focus();
                                            return false;
                                        }
                                    }

                                    if (jQuery('#pass1').val() == "") {
                                        jQuery('#pass1').focus();
                                        return false;
                                    }

                                    if (jQuery('#pass1').val() != jQuery('#pass2').val()) {
                                        jQuery('#pass2').focus();
                                        return false;
                                    }
                                });

                                //second step - plan customization
                                jQuery('.fldchooseplan').click(function () {
                                    return true;
                                });

                            });
        </script>
        <!-- END JAVASCRIPTS -->
    </body>
    <!-- END BODY -->
</html>