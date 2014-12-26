<?php
/**
 * Renders register user form
 * waits prepared vars:
 * $package, $current_step, $prev_step, $package_id
 */
?>
<!-- BEGIN LOGIN FORM -->


<?php if (!empty($errors)) : ?>

	<strong style="color: #f00">
		<?php echo implode('<br />', $errors) ?>
	</strong>

<?php endif; ?>

<form id="regform" class="form-vertical no-padding no-margin" method="post"
      action="<?php echo admin_url() ?>?package=<?php echo $package_id ?>" style="overflow: hidden; clear: both">
	<h4>Sign Up for "<?php echo $package['title'] ?>" ( <?php echo $package['domains'] ?> domains included)</h4>
	<input type="hidden" name="cur_register_step" value="start"/>
	<input type="hidden" name="prev_register_step" value="<?php echo $current_step ?>"/>

	<div>
		<div class="row">
			<label for="name">Business Name:</label>

			<div class="text"><input id="bname" name="bname"
			                         value="<?php echo (isset($_POST["bname"])) ? $_POST["bname"] : "" ?>" type="text"
			                         required email for_tootltip="Business Name"/></div>
		</div>


		<div class="row">
			<label for="name">E-mail:</label>

			<div class="text"><input id="email" name="email"
			                         value="<?php echo (isset($_POST["email"])) ? $_POST["email"] : "" ?>" type="text"
			                         required email for_tootltip="E-mail"/></div>
		</div>

		<div class="row">
			<label for="name">Website:</label>

			<div class="text"><input id="site" name="site"
			                         value="<?php echo (isset($_POST["site"])) ? $_POST["site"] : "" ?>" type="text"
			                         required email for_tootltip="Website"/></div>
		</div>

		<div class="row">
			<label for="name">Password:</label>

			<div class="text"><input id="pass1" name="pass1" type="password" required for_tootltip="Password"/></div>
		</div>

		<div class="row">
			<label for="name">Password Again:</label>

			<div class="text"><input id="pass2" name="pass2" type="password" required for_tootltip="Password Again"/>
			</div>
		</div>

		<div class="row">
			<label for="name">Enter Symbols Below (
				<a href="#" onclick="jQuery('#captcha_image').trigger( 'click' ); return false;">refresh image</a>):
			</label>
			<?php
			// output CAPTCHA img + input box
			echo captcha::form("Write symbols above");
			?>
		</div>

	</div>

	<input type="submit" id="login-btn" class="btn btn-block btn-inverse fldsubmitLicense" value="Sign Up"/>
</form>
<!-- END LOGIN FORM -->
