# EDD Language Packs

Allows shipping of language pack for themes and plugins sold via EDD.

Requires: EDD & EDD Software Licenses

## Settings

There are three settings on the single download page.

1. Directory URL for all of the translations for that theme or plugin
2. Defining the type of download - Is it a plugin or theme?
3. Last but not least the supported languages

## Language Packs

The language packs need to be in zip format. They should contain the mo and po files. The po files are recommended but not required.

The zip file and the mo and po files should be named in a specific format to work. `plugin-slug-language_code.zip/.mo/.po`
For example the slug for this plugin is edd-language-packs and I have a German(Switzerland) translation so the file name would be `edd-language-packs-de_CH.zip`

## Updater Classs

### Theme

For the theme update code you need to extend the function `theme_update_transient()` in `theme-updater-class.php` line 91 with:
```php
edd_lp_merge_translations( $value, $update_data );
```

### Plugin

For the plugin update code you need to extend the function `check_update()` in `EDD_SL_Plugin_Updater.php` line 97  with:
```php
edd_lp_merge_translations( $_transient_data, $version_info );
```

This function works for both the theme and plugin updater
```php
/**
 * Update the response with own language packs.
 *
 * @param array $response    Update info on all plugins.
 * @param array $update_data Update for own plugins.
 *
 * @return array             Update info on all plugins.
 *
 * @since 0.1.0
 */
function edd_lp_merge_translations( $response, $update_data ) {
	$update_data = json_decode( json_encode( $update_data ), true );
	// If no there no translations return early.
	if ( empty( $update_data['translations'] ) ) {
		return $response;
	}

	if ( property_exists( $response, 'translations' ) ) {
		$response->translations = array_merge(
			$response->translations,
			$update_data['translations']
		);
	} else {
		$response->translations = $update_data['translations'];
	}

	return $response;
}
```

## Technical details

The slug that you define in the config of `theme-updater.php` or `plugin-updater.php` is used as the slug. This should be the same as the registered text domain. If the slug in the update config and the text domain are not the same you can hook into the action `edd_sl_license_response` after priority 10 to change the slug.

The updated date is automatically fetched via `wp_remote_retrieve_header()`. So not to ping the translation pack link every time there is an update check the translation packs for a download are cached using transients.

The cache is deleted when updating the download.
