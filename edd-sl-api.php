<?php
/**
 * Hook into EDD Software license response filter and add the language packs to the response
 *
 * @param array $response Array of update information for download
 * @param array $download Array of download data for the single download
 *
 * @return array          Array of update information for download
 *
 * @since 0.1.0
 */
function edd_lp_add_translations_license_response( $response, $download ) {
	if ( $download->post_title === $response['name'] ) {

		$translations = get_transient( 'language-packs-' . $download->ID );

		if ( $translations ) {
			$response['translations'] = $translations;
			return $response;
		}

		$languages = get_post_meta( $download->ID, 'edd_lp_languages', true );
		$translations = array();

		foreach ( $languages as $key => $language ) {
			$directory     = get_post_meta( $download->ID, 'edd_lp_directory', true );
			$package_url   = $directory . $response['slug'] . '-' . $language . '.zip';
			$last_modified = edd_lp_get_last_modified( $package_url );

			if ( ! $last_modified ) {
				continue;
			}
			$translations[] = array(
				'type'       => get_post_meta( $download->ID, '_edd_lp_type', true ),
				'slug'       => $response['slug'],
				'language'   => $language,
				'version'    => get_post_meta( $download->ID, '_edd_sl_version', true ),
				'updated'    => $last_modified,
				'package'    => $package_url,
				'autoupdate' => 1
			);
		}
		set_transient( 'language-packs-' . $download->ID, $translations, 24 * HOUR_IN_SECONDS );
	}

	$response['translations'] = $translations;
	error_log(print_r($response,true));
	return $response;
};
add_filter( 'edd_sl_license_response', 'edd_lp_add_translations_license_response', 10, 2 );

/**
 * Fetch the last modified date for the language pack url
 *
 * @param string $package_url Translate pack zip URL
 *
 * @return string             Last modified date in `Y-m-d H:i:s` format
 *
 * @since 0.1.0
 */
function edd_lp_get_last_modified( $package_url ) {
	$response = wp_remote_get( $package_url );
	$last_modified = wp_remote_retrieve_header( $response, 'last-modified' );
	if ( ! $last_modified ) {
		error_log( $package_url . ': ' . print_r( $response['response'], true ) );
		return false;
	}
	return date( 'Y-m-d H:i:s', strtotime( $last_modified ) );
}