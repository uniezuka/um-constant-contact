<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       NA
 * @since      1.0.0
 *
 * @package    Um_Constant_Contact
 * @subpackage Um_Constant_Contact/admin/partials
 */
?>

<div class="wrap">
    <h2>Ultimate Member - Constant Contact</h2>
    <h3>Registration Page</h3>
	<span>Create contacts after registration to this lists.</span>
    <form method="post" action="options.php">
        <ul class="clear">
			<?php
				settings_fields( $this->plugin_name . '_options' );   
				do_settings_sections( $this->plugin_name );
				submit_button(); 
			?>
		</ul>
    </form>
</div> 