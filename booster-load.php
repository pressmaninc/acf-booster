<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action( 'acf/init', 'loading_booster' );

function loading_booster() {

	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	$parent = acf_add_options_page(
		array(
			'page_title' => __( 'ACF-booster' ),
			'menu_title' => __( 'ACF-booster' ),
			'menu_slug'  => 'acf-booster',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);
	if ( get_field( 'ngword-control', 'option' ) ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => 'NG word setting',
				'menu_title'  => 'NG word setting',
				'menu_slug'   => 'acf-booster-ng-setting',
				'parent_slug' => $parent['menu_slug'],
			)
		);
		acf_add_local_field_group(
			array(
				'key'                   => 'group_5d9488ca0f3ef',
				'title'                 => 'ng-words',
				'fields'                => array(
					array(
						'key'               => acfb_word_key,
						'label'             => 'NG word list',
						'name'              => 'word-list',
						'type'              => 'textarea',
						'instructions'      => 'Write NG words separated by commas.',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'acfe_permissions'  => '',
						'default_value'     => '',
						'placeholder'       => '',
						'maxlength'         => '',
						'rows'              => '',
						'new_lines'         => '',
						'acfe_validate'     => '',
						'acfe_update'       => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'acf-booster-ng-setting',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'left',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
				'acfe_display_title'    => '',
				'acfe_autosync'         => '',
				'acfe_permissions'      => '',
				'acfe_note'             => '',
				'acfe_meta'             => '',
			)
		);
	}
	acf_add_local_field_group(
		array(
			'key'                   => 'group_5d948372d70d0',
			'title'                 => 'function setting',
			'fields'                => array(
				array(
					'key'               => 'field_5d94859f44edc',
					'label'             => 'NG Word Checker',
					'name'              => 'ngword-control',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'acfe_permissions'  => '',
					'message'           => 'Add the ability to set prohibited characters in custom fields',
					'default_value'     => 0,
					'ui'                => 1,
					'ui_on_text'        => 'Enable',
					'ui_off_text'       => 'Disable',
					'acfe_validate'     => '',
					'acfe_update'       => '',
				),
				array(
					'key'               => 'field_5d94871e4ff32',
					'label'             => 'Character Counter',
					'name'              => 'counter-control',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'acfe_permissions'  => '',
					'message'           => 'Add a character counter to the custom field input screen',
					'default_value'     => 0,
					'ui'                => 1,
					'ui_on_text'        => 'Enable',
					'ui_off_text'       => 'Disable',
					'acfe_validate'     => '',
					'acfe_update'       => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-booster',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'acfe_display_title'    => '',
			'acfe_autosync'         => '',
			'acfe_permissions'      => '',
			'acfe_note'             => '',
			'acfe_meta'             => '',
		)
	);
}
