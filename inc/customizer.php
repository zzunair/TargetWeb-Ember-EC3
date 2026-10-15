<?php
/**
 * Customizer registration.
 *
 * @package Forge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'customize_register',
	function ( $wp ) {
		$wp->add_panel( 'forge', array( 'title' => 'TargetWeb - Ember', 'priority' => 20 ) );
		foreach ( forge_fields() as $sid => $s ) {
			$section = 'forge_' . $sid;
			$wp->add_section( $section, array( 'title' => $s['title'], 'panel' => 'forge' ) );
			foreach ( $s['fields'] as $key => $d ) {
				$wp->add_setting(
					$key,
					array(
						'default'           => $d['default'],
						'sanitize_callback' => function ( $v ) use ( $d ) {
							return forge_sanitize_field( $v, $d );
						},
					)
				);
				$args = array( 'label' => $d['label'], 'section' => $section, 'settings' => $key );
				switch ( $d['type'] ) {
					case 'image':
						$wp->add_control( new WP_Customize_Media_Control( $wp, $key, $args + array( 'mime_type' => 'image' ) ) );
						break;
					case 'color':
						$wp->add_control( new WP_Customize_Color_Control( $wp, $key, $args ) );
						break;
					case 'select':
						$wp->add_control( $key, $args + array( 'type' => 'select', 'choices' => $d['choices'] ) );
						break;
					case 'number':
						$wp->add_control( $key, $args + array( 'type' => 'number', 'input_attrs' => array( 'min' => isset( $d['min'] ) ? $d['min'] : 0, 'max' => isset( $d['max'] ) ? $d['max'] : 100000 ) ) );
						break;
					case 'checkbox':
					case 'textarea':
					case 'url':
					case 'email':
						$wp->add_control( $key, $args + array( 'type' => $d['type'] ) );
						break;
					default:
						$wp->add_control( $key, $args + array( 'type' => 'text' ) );
				}
			}
		}
	}
);
