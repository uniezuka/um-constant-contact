<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       NA
 * @since      1.0.0
 *
 * @package    Um_Constant_Contact
 * @subpackage Um_Constant_Contact/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Um_Constant_Contact
 * @subpackage Um_Constant_Contact/includes
 * @author     Junie Lorenzo <junie.lorenzo@gmail.com>
 */
class Um_Constant_Contact {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Um_Constant_Contact_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	protected $plugin_active;
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'um-constant-contact';
		$this->version = '1.0.0';
		$this->plugin_active = false;
		
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_hooks();
	}
	
	private function define_hooks() {
		$this->loader->add_action( 'init', $this, 'plugin_check', 1 );
		$this->loader->add_action( 'um_post_registration_global_hook', $this, 'umcc_post_registration_global_hook' );
	}
	
	public function plugin_check() {
		$this->plugin_active = true;
		$notice = "";
		if ( !class_exists('UM_API') ) {
			$notice .= '<p>This extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a></p>';
			$this->plugin_active = false;
		}
		if ( !class_exists('CTCTCF7') ) {
			$notice .= '<p>This extension requires the Contact Form 7 Newsletter plugin</p>';
			$this->plugin_active = false;
		}
		
		if (!$this->plugin_active)
			$this->add_notice( $notice );

	}
	
	private function add_notice( $msg ) {
		
		if ( !is_admin() ) return;
		
		echo '<div class="error"><p>' . $msg . '</p></div>';
		
	}
	
	public function umcc_post_registration_global_hook($user_id) {
		if (!$this->plugin_active) return;
		if (!class_exists('CTCT_SuperClass')) return;

		$user = get_user_by( 'id', $user_id );
		$options = get_option($this->plugin_name);
		$selectedLists = !empty( $options['lists'] ) ? (array)$options['lists'] : array();
		
		if (empty($selectedLists)) return;
		
		if ( !empty( $user ) ) {	
			$contact = array();
			$contact['first_name'] = $user->first_name;
			$contact['last_name'] = $user->last_name;
			$contact['email_address'] = $user->user_email;
			//$contact['home_number'] = $user->phone_number;
			$contact['opt_in_source'] = 'ACTION_BY_CONTACT';
			
			if ( empty( $contact['email_address'] ) || ! is_email( $contact['email_address'] ) )  return;
			
			$CTCT_SuperClass = new CTCT_SuperClass;
			$Contact = $CTCT_SuperClass->CC_Contact( $contact );
			$Contact->setOptInSource( $contact['opt_in_source'] );
			
			foreach (CTCT_SuperClass::getAvailableLists() as $key => $value) {
				if (in_array($value['id'], $selectedLists)) {
					$Contact->setLists( $value['link'] );
				}
			}

			if ( $Contact->getLists() ) {
				$response = $CTCT_SuperClass->CC_ContactsCollection()->createContact( $Contact, false );
			}
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Um_Constant_Contact_Loader. Orchestrates the hooks of the plugin.
	 * - Um_Constant_Contact_Admin. Defines all hooks for the admin area.
	 * - Um_Constant_Contact_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-um-constant-contact-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-um-constant-contact-admin.php';

		$this->loader = new Um_Constant_Contact_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Um_Constant_Contact_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'load_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'page_init' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Um_Constant_Contact_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
