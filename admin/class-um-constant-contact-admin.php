<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       NA
 * @since      1.0.0
 *
 * @package    Um_Constant_Contact
 * @subpackage Um_Constant_Contact/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Um_Constant_Contact
 * @subpackage Um_Constant_Contact/admin
 * @author     Junie Lorenzo <junie.lorenzo@gmail.com>
 */
class Um_Constant_Contact_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Um_Constant_Contact_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Um_Constant_Contact_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/um-constant-contact-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Um_Constant_Contact_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Um_Constant_Contact_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/um-constant-contact-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function load_menu() {
		add_submenu_page( 'ultimatemember', 'Ultimate Member Constant Contact Settings', 'Constant Contact', 'manage_options', $this->plugin_name, array ($this, 'load_content'));
	}

	public function load_content() {
		require_once plugin_dir_path( __FILE__ ). 'partials/um-constant-contact-admin-display.php';
	}
	
	public function page_init() {   
		
		register_setting(
            $this->plugin_name . '_options',
            $this->plugin_name
        );
		
		add_settings_section(
            'registration_section',
            '',
            '',
            $this->plugin_name
        );  
		
		add_settings_field(
            'lists',
            null,  
            array( $this, 'load_list_callback_content' ), 
            $this->plugin_name,
			'registration_section'     
        ); 
		    
    }
	
	public function load_list_callback_content() {
		$CTCT_SuperClass = new CTCT_SuperClass;
		$lists = $CTCT_SuperClass::getAvailableLists();
		
		$options = get_option($this->plugin_name);
		$selectedLists = !empty( $options['lists'] ) ? (array)$options['lists'] : array();
		$output = '<ul class="clear">';
		$template = '<li><label><input type="checkbox" class="option" value="{value}" name="{name}" {checked} /> {label}</label></li>';

		foreach($lists as $list) {
			$list_output = $template;

			$value = $list['id'];
			$label = esc_html( $list['name'] );

			$checked = checked( ( is_array( $list ) && in_array( $list['id'], $selectedLists ) ), true, false );
			$list_output = str_replace( '{name}', $this->plugin_name . '[lists][]' , $list_output );
			$list_output = str_replace( '{value}', $value, $list_output );
			$list_output = str_replace( '{checked}', $checked, $list_output );
			$list_output = str_replace( '{label}', $label, $list_output );

			$output .= $list_output . '</ul>';
		}
		
		echo $output;
	}

}
