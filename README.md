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
For the theme update code you need to extend the function `theme_update_transient()` in `theme-updater-class.php` with:
```php
if ( array_key_exists( 'translations', $update_data ) ) {
	foreach ( $value->translations as $key => $value) {
		$update_data['translations'][] = $value;
	}
	$value->translations = (object) $update_data['translations'];
}
```

For the plugin update code you need to extend the function `check_update()` in `EDD_SL_Plugin_Updater.php` with:
```php
if ( isset( $version_info->translations ) ) {
		if ( isset( $_transient_data->translations ) ) {
		foreach ( $_transient_data->translations as $key => $value) {
			$version_info->translations[] = $value;
		}
	}
	$_transient_data->translations = $version_info->translations;
}
```

## Technical details

The slug that you define in the config of `theme-updater.php` or `plugin-updater.php` is used as the slug. This should be the same as the registered text domain. If the slug in the update config and the text domain are not the same you can hook into the action `edd_sl_license_response` after priority 10 to change the slug.

The updated date is automatically fetched via `wp_remote_retrieve_header()`. So not to ping the translation pack link every time there is an update check the translation packs for a download are cached using transients.

The cache is deleted when updating the download.
