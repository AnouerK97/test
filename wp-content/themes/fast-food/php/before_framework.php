<?php

if ( ! function_exists( 'boldthemes_customize_header_style' ) ) {
	function boldthemes_customize_header_style( $wp_customize ) {
		$wp_customize->add_setting( BoldThemesFramework::$pfx . '_theme_options[header_style]', array(
			'default'           => BoldThemes_Customize_Default::$data['header_style'],
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'header_style', array(
			'label'     => esc_html__( 'Header Style', 'fast-food' ),
			'section'   => BoldThemesFramework::$pfx . '_header_footer_section',
			'settings'  => BoldThemesFramework::$pfx . '_theme_options[header_style]',
			'priority'  => 62,
			'type'      => 'select',
			'choices'   => array(
				'no_change'       => esc_html__( 'Default', 'fast-food' ),
				'btAccentDarkHeader' => esc_html__( 'Accent + Dark', 'fast-food' ),
				'btAccentLightHeader' => esc_html__( 'Accent + Light', 'fast-food' ),
				'btLightAccentHeader' => esc_html__( 'Light + Accent', 'fast-food' ),
				'btLightHeader' => esc_html__( 'Light + Dark elements', 'fast-food' )				
			)
		));
	}
}
add_action( 'customize_register', 'boldthemes_customize_header_style' );

if ( ! function_exists( 'boldthemes_customize_page_width' ) ) {
	function boldthemes_customize_page_width( $wp_customize ) {
		
		$wp_customize->add_setting( BoldThemesFramework::$pfx . '_theme_options[page_width]', array(
			'default'           => BoldThemes_Customize_Default::$data['page_width'],
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_control( 'page_width', array(
			'label'     => esc_html__( 'Page Width', 'fast-food' ),
			'section'   => BoldThemesFramework::$pfx . '_general_section',
			'settings'  => BoldThemesFramework::$pfx . '_theme_options[page_width]',
			'priority'  => 95,
			'type'      => 'select',
			'choices'   => array(
				'no_change'       => esc_html__( 'Default', 'fast-food' ),
				'btBoxedPage' 	=> esc_html__( 'Boxed', 'fast-food' ),
				'btBorderedPage' 	=> esc_html__( 'With border', 'fast-food' )				
			)
		));
	}
}

add_action( 'customize_register', 'boldthemes_customize_page_width' );

/**
 * Loop columns
 */
if ( ! function_exists( 'boldthemes_loop_shop_columns' ) ) {
	function boldthemes_loop_shop_columns() {
		return 3; // 3 products per row
	}
}

/**
 * Change number of related products
 */
if ( ! function_exists( 'boldthemes_change_number_related_products' ) ) {
	function boldthemes_change_number_related_products( $args ) {
		$args['posts_per_page'] = 3; // # of related products
		$args['columns'] = 3; // # of columns per row
		return $args;
	}
}

/**
 * Extra classes
 */
add_filter( 'boldthemes_extra_class', 'food_haus_extra_class' );
if ( ! function_exists( 'food_haus_extra_class' ) ) {
	function food_haus_extra_class( $extra_class ) {
		if ( boldthemes_get_option( 'buttons_shape' ) == "no_change" ) {
			$extra_class[] = 'btHardRoundedButtons' ;
		}
		return $extra_class;
	}
	
}

/**
 * Charts class
 */
add_filter( 'visualizer-chart-wrapper-class', 'food_haus_charts_class', 10, 2 );
if ( ! function_exists( 'food_haus_charts_class' ) ) {
	function food_haus_charts_class( $class, $id ) {
		return 'btVisualizer';
	}
}

/**
 * Product headline size
 */
 
add_filter( 'boldthemes_product_headline_size', 'boldthemes_product_headline_size' );
if ( ! function_exists( 'boldthemes_product_headline_size' ) ) {
	function boldthemes_product_headline_size( $size ) {
		return 'extralarge';
	}
}

/**
 * Header headline size
 */
add_filter( 'boldthemes_header_headline_size', 'boldthemes_header_headline_size' );
if ( ! function_exists( 'boldthemes_header_headline_size' ) ) {
	function boldthemes_header_headline_size( $size ) {
		return 'extralarge';
	}
}

/**
 * Header headline output
 */
if ( ! function_exists( 'boldthemes_header_headline' ) ) {
	function boldthemes_header_headline( $arg = array() ) {
		
		BoldThemesFramework::$hide_headline = boldthemes_get_option( 'hide_headline' );
		
		if ( ( ! BoldThemesFramework::$hide_headline && ! is_404() ) ) {
			$extra_class = '';
			
			$dash  = '';
			$use_dash = boldthemes_get_option( 'sidebar_use_dash' );
			if ( $use_dash ) $dash  = apply_filters( 'boldthemes_header_headline_dash', 'bottom' );
			$title = is_front_page() ? get_bloginfo( 'description' ) : wp_title( '', false );
			
			if ( BoldThemesFramework::$page_for_header_id != '' ) {
				$feat_image = wp_get_attachment_url( get_post_thumbnail_id( BoldThemesFramework::$page_for_header_id ) );
				
				$excerpt = boldthemes_get_the_excerpt( BoldThemesFramework::$page_for_header_id );
				if ( ! $feat_image ) {
					if ( is_singular() &&  !is_singular( "product" ) ) {
						$feat_image = wp_get_attachment_url( get_post_thumbnail_id() );
					} else {
						$feat_image = false;
					}
				}
			} else {
				if ( is_singular() ) {
					$feat_image = wp_get_attachment_url( get_post_thumbnail_id() );
				} else {
					$feat_image = false;
				}
				$excerpt = boldthemes_get_the_excerpt( get_the_ID() );
			}
			
			$parallax = isset( $arg['parallax'] ) ? $arg['parallax'] : '0.8';
			$parallax_class = 'btParallax';
			if ( wp_is_mobile() ) {
				$parallax = 0;
				$parallax_class = '';
			}
			
			$supertitle = '';
			$subtitle = $excerpt;
			
			$breadcrumbs = isset( $arg['breadcrumbs'] ) ? $arg['breadcrumbs'] : true;

			// yoast plugin checking
			if ( $title != '' && is_singular() ) {
				if ( class_exists( 'WPSEO_Options' ) ) {
					$title = get_the_title();
				}
			}

			
			if ( $breadcrumbs ) {
				$heading_args = boldthemes_breadcrumbs( false, $title, $subtitle );
				$supertitle = $heading_args['supertitle'];
				$title = $heading_args['title'];
				$subtitle = $heading_args['subtitle'];
			}
			
			if ( $title != '' || $supertitle != '' || $subtitle != '' ) {
				$extra_class .= boldthemes_get_option( 'below_menu' ) ? ' topLargeSpaced' : ' topSemiSpaced';
				$extra_class .= boldthemes_get_option( 'menu_type' ) == "hCenter" ? ' btTextCenter' : ' btTextLeft';
				$extra_class .= $feat_image ? ' wBackground cover ' . $parallax_class . ' btDarkSkin btBackgroundOverlay btSolidDarkBackground ' : ' ';
				echo '<section class="boldSection bottomSemiSpaced btPageHeadline gutter ' . esc_attr( $extra_class ) . '" style="background-image:url(' . esc_url_raw( $feat_image ) . ')" data-parallax="' . esc_attr( $parallax ) . '" data-parallax-offset="0"><div class="port">';
				echo boldthemes_get_heading_html( $supertitle, $title, $subtitle, apply_filters( 'boldthemes_header_headline_size', 'large' ), $dash, '', '' );
				echo '</div></section>';
			}
			
		}
 	}
}
