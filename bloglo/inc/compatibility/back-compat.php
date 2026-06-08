<?php
/**
 * Theme back compatibility functionality
 *
 * Migrates legacy slug-based selections to IDs for select controls in the Customizer.
 *
 * @package Bloglo
 * @author  Peregrine Themes
 * @since   1.0.24
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme_mod filters for each setting in the migration map.
 *
 * @since  1.0.15
 * @return void
 */
function bloglo_register_filters() {
	foreach ( bloglo_get_migration_map() as $setting_id => $config ) {
		add_filter(
			"theme_mod_{$setting_id}",
			function ( $value ) use ( $setting_id, $config ) {
				return bloglo_maybe_migrate( $value, $setting_id, $config );
			},
			20
		);
	}
}
add_action( 'after_setup_theme', 'bloglo_register_filters', 20 );
	/**
	 * Map of select settings to their migration rules.
	 *
	 * @since  1.0.15
	 * @return array
	 */
function bloglo_get_migration_map() {
	return apply_filters(
		'bloglo_migration_map',
		array(
			'bloglo_ticker_category'            => array(
				'type'     => 'term',
				'taxonomy' => 'category',
			),
			'bloglo_hero_hover_slider_category' => array(
				'type'     => 'term',
				'taxonomy' => 'category',
			),
			'bloglo_pyml_category'              => array(
				'type'     => 'term',
				'taxonomy' => 'category',
			),
		)
	);
}

	/**
	 * Migrate a setting value from slugs to term IDs if needed.
	 *
	 * @since  1.0.15
	 * @param  mixed  $value      Current setting value.
	 * @param  string $setting_id Setting ID (unused here, available for logging/extension).
	 * @param  array  $config     Migration config ( 'type', 'taxonomy' ).
	 * @return mixed Converted IDs array, original value if already numeric, or empty array on failure.
	 */
function bloglo_maybe_migrate( $value, $setting_id, array $config ) {
	if ( empty( $value ) ) {
		return $value;
	}
	$value_array = bloglo_normalize_to_array( $value );

	if ( bloglo_all_numeric( $value_array ) ) {
		return $value;
	}

	$converted = bloglo_convert_slugs_to_ids( $value_array, $config );
	$converted = array_values( array_unique( array_filter( $converted ) ) );

	return ! empty( $converted ) ? $converted : array();
}

	/**
	 * Normalize a value to an array of scalar items.
	 *
	 * @since  1.0.15
	 * @param  mixed $value Input value, which may be a comma-separated string, an array, or a single scalar.
	 * @return array
	 */
function bloglo_normalize_to_array( $value ) {
	if ( is_string( $value ) ) {
		return array_filter( array_map( 'trim', explode( ',', $value ) ) );
	}

	if ( ! is_array( $value ) ) {
		return array( $value );
	}

	return $value;
}

	/**
	 * Check whether every item in an array is already a numeric string or integer.
	 *
	 * @since  1.0.15
	 * @param  array $items Array of items to check.
	 * @return bool
	 */
function bloglo_all_numeric( array $items ) {
	foreach ( $items as $item ) {
		$str = is_scalar( $item ) ? (string) $item : '';
		if ( '' !== $str && ! ctype_digit( $str ) ) {
			return false;
		}
	}
	return true;
}

	/**
	 * Convert an array of mixed slug/numeric items to term IDs.
	 *
	 * @since  1.0.15
	 * @param  array $items  Array of items to convert (may contain slugs or numeric strings).
	 * @param  array $config Migration config.
	 * @return int[]
	 */
function bloglo_convert_slugs_to_ids( array $items, array $config ) {
	$converted = array();

	foreach ( $items as $item ) {
		$str = is_scalar( $item ) ? (string) $item : '';

		if ( '' === $str ) {
			continue;
		}

		// Already numeric — keep as-is.
		if ( ctype_digit( $str ) ) {
			$converted[] = (int) $str;
			continue;
		}

		// Resolve slug to ID.
		if ( 'term' === $config['type'] ) {
			$id = bloglo_resolve_term_id( $str, $config['taxonomy'] );
			if ( $id ) {
				$converted[] = $id;
			}
		} elseif ( 'post' === $config['type'] ) {
			$post = get_page_by_path( $str, OBJECT, $config['post_type'] );
			if ( $post && ! is_wp_error( $post ) ) {
				$converted[] = (int) $post->ID;
			}
		}
	}
	return $converted;
}

	/**
	 * Attempt to resolve a term slug (or name) to its term ID.
	 *
	 * @since  1.0.15
	 * @param  string $slug     Slug or name to look up.
	 * @param  string $taxonomy Taxonomy name.
	 * @return int|null Term ID on success, null on failure.
	 */
function bloglo_resolve_term_id( $slug, $taxonomy ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}

	// Fallback: try by name.
	$term = get_term_by( 'name', $slug, $taxonomy );
	if ( $term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}

	return null;
}
