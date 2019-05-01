<?php
/**
 * Template Handling functions
 *
 * @package    BuddyCommerce
 * @subpackage Core/Templates
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get the base directory where our templates are stored.
 * It is relative to the theme too.
 * For example, if we specify 'buddycommerce/template-pack' the templates will be searched in
 *  1. themes/twentyseventeen(or the current active child theme or theme)/buddycommerce/template-pack
 *  2. If not found in child theme and not found in parent theme,
 *     It will fallback to plugins/buddycommerce/templates/buddycommerce/template-pack
 *
 * @return string
 */
function bcommerce_get_template_base_dir() {
	return apply_filters( 'bcommerce_template_base_dir', 'buddycommerce/default' );
}

/**
 * Get template part (for templates like the user-home). Loads the template.
 *
 * @param string $slug template part name.
 * @param string $name template part part name(optional, default:'').
 * @param string $fallback_path Fallback template directory base path.
 */
function bcommerce_get_template_part( $slug, $name = '', $fallback_path = '' ) {

	$templates = array();

	if ( $name ) {
		$templates = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	$template = bcommerce_locate_template( $templates, false, $fallback_path );

	$template = apply_filters( 'bcommerce_get_template_part', $template, $slug, $name, $fallback_path );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        your-child-theme  /    buddycommerce    / default(current template pack) /    $template_name
 *        your-theme        /    buddycommerce    / default(current template pack) /    $template_name
 *
 *        $default_path     /    buddycommerce    / default(current template pack) /    $template_name
 *
 * @see bcommerce_get_template_part() if you are looking to load template parts.
 *      This function is aimed at plugin developer.
 *
 * @param array  $template_files array of php templates.
 * @param bool   $load whether to load or return the path.
 * @param string $default_path (default: ''), path to use as base. Allows plugins to override it.
 *
 * @return string
 */
function bcommerce_locate_template( $template_files, $load = false, $default_path = '' ) {
	$base_dir = bcommerce_get_template_base_dir();

	// Fallback to BuddyCommerce included plugin template path.
	if ( ! $default_path ) {
		$default_path = buddycommerce()->path . 'templates/' . $base_dir;
	}

	$default_path = untrailingslashit( $default_path );

	$located = '';

	// the array looks like an array of relative paths ee.g user/xyx.php. etc.
	// remove any empty entry.
	$template_files = array_filter( (array) $template_files );

	foreach ( $template_files as $template_file ) {

		if ( ! $template_file ) {
			continue;
		}

		$theme_template_file = $base_dir . '/' . $template_file;

		$located = locate_template( array( $theme_template_file ), false );

		if ( ! $located && is_readable( $default_path . '/' . $template_file ) ) {
			$located = $default_path . '/' . $template_file;
		}

		if ( $located ) {
			break;
		}
	}

	if ( $load && $located ) {
		load_template( $located, false );
	}

	// Return what we found.
	return apply_filters( 'bcommerce_located_template', $located, $template_files, $default_path );
}

/**
 * Locate asset from theme template dirs/plugin.
 *
 * It loads assets relative to buddycommerce/{current_template_pack}/
 *
 * @param string $file file name.
 * @param string $default_url default fallback url if asset is not found in themes.
 *
 * @return string
 */
function bcommerce_locate_asset( $file, $default_url = '' ) {
	$base_dir = bcommerce_get_template_base_dir();

	// Fallback to BuddyCommerce included plugin template path.
	if ( ! $default_url ) {
		$default_url = buddycommerce()->url . 'templates';
	}

	$file        = ltrim( $file, '/' );
	$default_url = rtrim( $default_url, '/' );

	// default/path.
	$asset_rel_path = $base_dir . '/' . $file;

	if ( empty( $file ) ) {
		$url = '';
	} elseif ( file_exists( get_stylesheet_directory() . '/' . $asset_rel_path ) ) {
		$url = get_stylesheet_directory_uri() . '/' . $asset_rel_path;
	} elseif ( file_exists( get_template_directory() . '/' . $asset_rel_path ) ) {
		$url = get_template_directory_uri() . '/' . $asset_rel_path;
	} else {
		$url = $default_url . '/' . $asset_rel_path;
	}

	return $url;
}
