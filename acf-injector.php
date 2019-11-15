<?php
/*
  Plugin Name: ACF injector
  Description: Add functionality to Advanced Custom Fields
  Version: 1.0
  Author: Shu Kano
  License: GNU GPL v2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class acf_injector {
	private static $instance;
	private function __construct() {
		//Load the setting screen
		require_once( plugin_dir_path( __FILE__ ) . 'injector-load.php' );
		//Load input counter function
		require_once( plugin_dir_path( __FILE__ ) . 'acf-input-counter.php' );
		//Adding settings to the field group editing screen
		add_action( 'acf/render_field_settings/type=textarea', array( $this, 'render_function_setting' ) );
		add_action( 'acf/render_field_settings/type=text', array( $this, 'render_function_setting' ) );
		//Load javascript on NG word detection and character counter
		add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'my_acf_field_group_admin_enqueue_scripts' ) );
		add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'scripts' ) );
		//Create endpoint to get NG word with ajax
		add_action( 'wp_ajax_check_words', array( $this, 'check_words' ) );
		add_action( 'wp_ajax_nopriv_check_words', array( $this, 'check_words' ) );
		//Check NG word list and input value, block posting if NG word is included
		add_action( 'acf/validate_value/type=text', array( $this, 'block_post' ), 10, 4 );
		add_action( 'acf/validate_value/type=textarea', array( $this, 'block_post' ), 10, 5 );
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
			self::$instance = new acf_injector();
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
		if ( get_field( 'counter-control', 'option' ) == '1' ) {
			wp_register_script( 'acf-input-counter.js', plugin_dir_url( __FILE__ ) . '/js/acf-input-counter.js', false, 1 );
			wp_enqueue_script( 'acf-input-counter.js' );
			wp_enqueue_style( 'acf-counter.css', plugins_url( 'acf-counter.css', __FILE__ ) );
		}
		if ( get_field( 'ngword-control', 'option' ) == '1' ) {
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
		if ( get_field( 'ngword-control', 'option' ) == '1' ) {
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

		if ( get_field( 'counter-control', 'option' ) == '1' ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Type Counter' ),
					'instructions' => __( 'display the input characters?' ),
					'name'         => 'show_count',
					'type'         => 'true_false',
					'ui'           => 1,
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
		if ( get_field( 'ngword-control', 'option' ) == '1' ) {
			wp_register_script( 'render-ngword-setting.js', plugin_dir_url( __FILE__ ) . '/js/render-ngword-setting.js', false, 1 );
			wp_enqueue_script( 'render-ngword-setting.js' );
		}
		if ( get_field( 'counter-control', 'option' ) == '1' ) {
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
		if ( get_field( 'ngword-control', 'option' ) == '1' ) {
			if ( isset( $_POST['target_field'] ) ) {
				$target = str_replace( 'acf-', '', $_POST['target_field'] );
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
		$currentpage = $_GET['page'];
		if ( ! $valid || $_GET['page'] == $currentpage ) {
			return $valid;
		}
		if ( get_field( 'ngword-control', 'option' ) == '1' ) {
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
$launch = acf_injector::getInstance();
