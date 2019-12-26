<?php
/*
	Plugin Name: ACF booster
	Description: Add functionality to Advanced Custom Fields
	Version: 1.0
	Author: PRESSMAN
	Author URI: https://www.pressman.ne.jp/
	Text Domain: acf-additional-hint
	Domain Path: /languages
	License: GPLv2 or later
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//NG words setting field key
define( 'acfb_word_key', 'field_5d9488ed2762d' );

class ACF_BOOSTER {
	private static $instance;
	private function __construct() {
		//Load the setting screen
		require_once( plugin_dir_path( __FILE__ ) . 'booster-load.php' );
		//Load input counter function
		require_once( plugin_dir_path( __FILE__ ) . 'acf-input-counter.php' );
		//Adding settings to the field group editing screen
		add_action( 'acf/render_field_settings/type=textarea', array( $this, 'render_function_setting' ), 7 );
		add_action( 'acf/render_field_settings/type=text', array( $this, 'render_function_setting' ), 7 );
		//Load javascript on NG word detection and character counter
		add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'my_acf_field_group_admin_enqueue_scripts' ), 8 );
		add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'scripts' ), 8 );
		//Create endpoint to get NG word with ajax
		add_action( 'wp_ajax_check_words', array( $this, 'check_words' ), 9 );
		add_action( 'wp_ajax_nopriv_check_words', array( $this, 'check_words' ), 9 );
		//Check NG word list and input value, block posting if NG word is included
		add_action( 'acf/validate_value/type=text', array( $this, 'block_post' ), 10, 4 );
		add_action( 'acf/validate_value/type=textarea', array( $this, 'block_post' ), 10, 4 );
	}
	/**
	 * getInstance
	 *
	 * For singleton patterning
	 *
	 * @param   void
	 * @return  void
	 */
	public static function getInstance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new ACF_BOOSTER();
		}
		return self::$instance;
	}
	/**
	 * scripts
	 *
	 * Check ON/OFF of each function and load javascript
	 *
	 * @param   void
	 * @return  void
	 */
	public function scripts() {
		if ( get_field( 'counter-control', 'option' ) ) {
			wp_register_script( 'acf-input-counter.js', plugin_dir_url( __FILE__ ) . '/js/acf-input-counter.js', false, 1 );
			wp_enqueue_script( 'acf-input-counter.js' );
			wp_enqueue_style( 'acf-counter.css', plugins_url( 'acf-counter.css', __FILE__ ) );
		}
		if ( get_field( 'ngword-control', 'option' ) ) {
			wp_register_script( 'acf-word-check.js', plugin_dir_url( __FILE__ ) . '/js/acf-word-check.js', false, 1 );
			wp_enqueue_script( 'acf-word-check.js' );
		}
	}
	/**
	 * render_function_setting
	 *
	 * Adding settings to the field group editing screen
	 *
	 * @param $field (array)
	 * @return (array)
	 */
	public function render_function_setting( $field ) {
		if ( get_field( 'ngword-control', 'option' ) ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'NG word function', 'ng-type-select' ),
					'instructions' => '',
					'type'         => 'radio',
					'name'         => 'ng-type-select',
					'choices'      => array(
						0 => __( 'Do not use in this field', 'ng-type-select' ),
						1 => __( 'Use NG word lists', 'ng-type-select' ),
						2 => __( 'Set a unique word', 'ng-type-select' ),
					),
					'layout'       => 'horizontal',
				)
			);
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Unique word' ),
					'instructions' => __( 'Enter words separated by a comma' ),
					'name'         => 'unique_ng_word',
					'type'         => 'text',
				),
				true
			);
		}

		if ( get_field( 'counter-control', 'option' ) ) {
			acf_render_field_setting(
				$field,
				array(
					'label'         => __( 'Type Counter' ),
					'instructions'  => __( 'display the input characters?' ),
					'name'          => 'show_count',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
				true
			);
		}

	}
	/**
	 * my_acf_field_group_admin_enqueue_scripts
	 *
	 * Check ON/OFF of each function and load field group setting screens javascript
	 *
	 * @param void
	 * @return void
	 */
	public function my_acf_field_group_admin_enqueue_scripts() {
		if ( get_field( 'ngword-control', 'option' ) ) {
			wp_register_script( 'render-ngword-setting.js', plugin_dir_url( __FILE__ ) . '/js/render-ngword-setting.js', false, 1 );
			wp_enqueue_script( 'render-ngword-setting.js' );
		}
		if ( get_field( 'counter-control', 'option' ) ) {
			wp_register_script( 'render-counter-setting.js', plugin_dir_url( __FILE__ ) . '/js/render-counter-setting.js', false, 1 );
			wp_enqueue_script( 'render-counter-setting.js' );
		}
	}
	/**
	 * check_words
	 *
	 * Create endpoint to get NG word with ajax
	 *
	 * @param void
	 * @return json
	 */
	public function check_words() {
		if ( get_field( 'ngword-control', 'option' ) ) {
			if ( isset( $_POST['target_field'] ) ) {
				$target = str_replace( 'acf-', '', sanitize_key( $_POST['target_field'] ) );
				$object = get_field_object( $target );
				if ( $object['ng-type-select'] == 2 ) {
					echo json_encode( $object['unique_ng_word'] );
				} elseif ( $object['ng-type-select'] == 1 ) {
					echo json_encode( get_field( 'word-list', 'option' ) );
				} else {
					echo json_encode( 'undefined' );
				}
			}
		}
		die();
	}
	/**
	 * block_post
	 *
	 * Block posts containing NG words on the server side
	 *
	 * @param $valid(mixed)
	 * @param $value(mixed)
	 * @param $field(array)
	 * @param $input(string)
	 * @return $valid
	 */
	public function block_post( $valid, $value, $field, $input ) {
		if ( ! $valid || $field['key'] == acfb_word_key ) {
			return $valid;
		}
		if ( get_field( 'ngword-control', 'option' ) ) {
			foreach ( $field as $key => $fieldkey ) {
				$object = get_field_object( $fieldkey );
				if ( $object['ng-type-select'] == 2 ) {
					$uq_word_list = explode( ',', $object['unique_ng_word'] );
					foreach ( $uq_word_list as $word ) {
						if ( ! empty( $word ) && strpos( $value, $word ) !== false ) {
							$valid = 'Contains NG word';
						}
					}
					return $valid;

				} elseif ( $object['ng-type-select'] == 1 ) {
					$setting_word = explode( ',', get_field( 'word-list', 'option' ) );
					foreach ( $setting_word as $word ) {
						if ( ! empty( $word ) && strpos( $value, $word ) !== false ) {
							$valid = 'Contains NG word';
						}
					}
					return $valid;
				}
			}
		}
	}
}
$launch = ACF_BOOSTER::getInstance();
