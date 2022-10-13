<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Visitor_Tracer
 * @subpackage Visitor_Tracer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Visitor_Tracer
 * @subpackage Visitor_Tracer/public
 * @author     Developer Junayed <admin@easeare.com>
 */
class Visitor_Tracer_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/visitor-tracer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/visitor-tracer-public.js', array( 'jquery' ), $this->version, true );

	}

	function wp_footer_callback($name){

		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
			$url = "https://"; 
		}else {
			$url = "http://";  
		}

		// Append the host(domain name, ip) to the URL.   
		$url.= $_SERVER['HTTP_HOST'];   
   
		// Append the requested resource location to the URL   
		$url.= $_SERVER['REQUEST_URI'];   

		wp_enqueue_script( $this->plugin_name );
		wp_localize_script( $this->plugin_name, "vt_ajax", array(
			'ajaxurl' => admin_url("admin-ajax.php"),
			'referer' => $url
		) );
	}

	function visitor_visit_counts(){
		if(isset($_POST['referer'])){
			date_default_timezone_set('America/Los_Angeles');

			$local_ips = ((isset($_POST['local_ips']))? implode(", ", $_POST['local_ips']): '');
			if(empty($local_ips)){
				$local_ips = $_SERVER['REMOTE_ADDR'];
			}
			$user_agent = $_SERVER ['HTTP_USER_AGENT'];
			$referer = sanitize_url($_POST['referer']);

			$visitor_id = null;
			if(isset($_SESSION['vt_visitor_id'])){
				$visitor_id = $_SESSION['vt_visitor_id'];
			}else{
				$visitor_id = time();
				$_SESSION['vt_visitor_id'] = $visitor_id;
			}

			if($visitor_id !== null){
				global $wpdb;
				
				$dbid = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}visitor_tracer WHERE visitor_id = $visitor_id");
				if($dbid){
					$wpdb->update($wpdb->prefix.'visitor_tracer', array(
						'exitPage' => $referer,
						'last_visit' => date("Y-m-d H:i:s")
					), array('ID' => $dbid, 'visitor_id' => $visitor_id), array('%s', '%s'), array('%d', '%d'));
				}else{
					$wpdb->insert($wpdb->prefix.'visitor_tracer', array(
						'visitor_id' => $visitor_id,
						'entryPage' => $referer,
						'exitPage' => $referer,
						'local_ip' => $local_ips,
						'user_agent' => $user_agent,
						'last_visit' => date("Y-m-d H:i:s"),
						'first_visit' => date("Y-m-d H:i:s")
					));
				}
			}

			if(!is_wp_error($wpdb )){
				echo json_encode(array("success" => "Success"));
				die;
			}
			
		}
		
		die;
	}

}
