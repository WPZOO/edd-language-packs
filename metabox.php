<?php
/**
 * Hook into `edd_metabox_fields_save` filter and add the download language pack fields
 *
 * @param array $fields Array of fields to save for EDD
 *
 * @return array        Array of fields to save for EDD
 *
 * @since 0.1.0
 */
function edd_lp_metabox_fields_save( $fields ) {

	$fields[] = 'edd_lp_directory';
	$fields[] = 'edd_lp_type';
	$fields[] = 'edd_lp_languages';

	return $fields;
}
add_filter( 'edd_metabox_fields_save', 'edd_lp_metabox_fields_save' );

/**
 * Hook into `edd_save_download` filter and sanitize the settinge before saving to the database
 *
 * @param string $post_id Post id of download
 *
 * @since 0.1.0
 */
function edd_lp_download_meta_box_save( $post_id, $post ) {

	if ( isset( $_POST['edd_lp_directory'] ) ) {
		update_post_meta( $post_id, 'edd_lp_directory', esc_url( $_POST['edd_lp_directory'] ) );
	} else {
		delete_post_meta( $post_id, 'edd_lp_directory' );
	}

	$types = array(
		'theme',
		'plugin',
	);
	if ( isset( $_POST['edd_lp_type'] ) && in_array( $_POST['edd_lp_type'], $types ) ) {
		update_post_meta( $post_id, 'edd_lp_type', sanitize_text_field( $_POST['edd_lp_type'] ) );
	} else {
		delete_post_meta( $post_id, 'edd_lp_type' );
	}

	require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
	$translations = array_keys( wp_get_available_translations() );
	if ( isset( $_POST['edd_lp_languages'] ) && array_intersect( $_POST['edd_lp_languages'], $translations ) ) {
		update_post_meta( $post_id, 'edd_lp_languages', $_POST['edd_lp_languages'] );
	} else {
		delete_post_meta( $post_id, 'edd_lp_languages' );
	}

	delete_transient( 'language-packs-' . $post_id );

}
add_action( 'edd_save_download', 'edd_lp_download_meta_box_save', 10, 2 );

/**
 * Language Pack Directory Section
 *
 * Output a single input box for the language pack directory.
 *
 * @param $post_id
 *
 * @since 0.1.0
 */
function edd_lp_render_directory_field( $post_id ) {
	$directory = get_post_meta( $post_id, 'edd_lp_directory', true );
?>
	<p>
		<strong><?php _e( 'Language Pack directory', 'edd-language-packs' ); ?></strong>
	</p>

	<div id="edd_lp_directory" class="edd_lp_directory">
		<?php
			$directory_args = array(
				'name'        => 'edd_lp_directory',
				'value'       => isset( $directory ) ? esc_attr( $directory ) : '',
				'placeholder' => __( 'Language packs directory URL', 'edd-language-packs' ),
				'class'       => 'edd-lp-directory-field'
			);
		?>

		<?php echo EDD()->html->text( $directory_args ); ?>

	</div>
<?php
}
add_action( 'edd_meta_box_files_fields', 'edd_lp_render_directory_field', 30 );

/**
 * Language Pack type dropdown
 *
 * Output a single dropdown to choose between the two `theme` and `plugin` type.
 *
 * @param $post_id
 *
 * @since 0.1.0
 */
function edd_lp_render_type_field( $post_id = 0 ) {
	$types = array(
		'theme'  => __( 'Theme', 'edd-language-packs' ),
		'plugin' => __( 'Plugin', 'edd-language-packs' ),
	);
	$type  = get_post_meta( $post_id, 'edd_lp_type', true );
?>
	<p>
		<strong><?php _e( 'Product Type', 'edd-language-packs' ); ?></strong>
	</p>
	<p>
		<?php echo EDD()->html->select( array(
			'options'          => $types,
			'name'             => 'edd_lp_type',
			'id'               => 'edd_lp_type',
			'selected'         => $type,
			'show_option_all'  => false,
			'show_option_none' => false
		) ); ?>
		<label for="edd_lp_type"><?php _e( 'Select a language pack type', 'edd-language-packs' ); ?></label>
	</p>
<?php
}
add_action( 'edd_meta_box_files_fields', 'edd_lp_render_type_field', 30 );

/**
 * Language Pack type dropdown
 *
 * Output a single dropdown to choose between the two `theme` and `plugin` type.
 *
 * @param $post_id
 *
 * @since 0.1.0
 */
function edd_lp_render_language_packs_field( $post_id ) {
	$translations = get_post_meta( $post_id, 'edd_lp_languages', true );
?>
	<div id="edd_lp_languages">
		<div id="edd_lp_languages_fields" class="edd_meta_table_wrap">
			<table class="widefat" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e( 'Supported languages', 'edd-language-packs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="edd_repeatable_product_wrapper">
						<td>
							<?php
							echo edd_lp_language_dropdown( array(
								'name'     => 'edd_lp_languages[]',
								'id'       => 'edd_lp_languages',
								'selected' => $translations,
								'multiple' => true,
								'chosen'   => true,
							) );
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php
}
add_action( 'edd_meta_box_files_fields', 'edd_lp_render_language_packs_field', 30 );

/**
 * Renders an HTML Dropdown of all the available languages
 *
 * @param  array  $args   Arguments for the dropdown
 *
 * @return string $output Languages dropdown
 *
 * @since 0.1.0
 */
function edd_lp_language_dropdown( $args = array() ) {

	$defaults = array(
		'name'        => 'languages',
		'id'          => 'languages',
		'class'       => '',
		'multiple'    => false,
		'selected'    => 0,
		'chosen'      => false,
		'placeholder' => __( 'Select a language', 'edd-language-packs' ),
	);

	$args = wp_parse_args( $args, $defaults );

	require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
	$translations = wp_get_available_translations();
	$options = array();

	if ( $translations ) {
		$options[0] = __( 'Select a language', 'edd-language-packs' );
		foreach ( $translations as $translation ) {
			$options[ $translation['language'] ] = esc_html( $translation['english_name'] );
		}
	} else {
		$options[0] = __( 'No languages found', 'edd-language-packs' );
	}

	// This ensures that any selected products are included in the drop down
	if( is_array( $args['selected'] ) ) {
		foreach( $args['selected'] as $language => $english_name ) {
			if( ! in_array( $language, $options ) ) {
				$options[ $language ] = $english_name;
			}
		}
	} elseif ( is_numeric( $args['selected'] ) && $args['selected'] !== 0 ) {
		if ( ! in_array( $args['selected'], $options ) ) {
			$options[ key( $args['selected'] ) ] = $args['selected'];
		}
	}

	$output = EDD()->html->select( array(
		'name'             => $args['name'],
		'selected'         => $args['selected'],
		'id'               => $args['id'],
		'class'            => $args['class'],
		'options'          => $options,
		'chosen'           => $args['chosen'],
		'multiple'         => $args['multiple'],
		'placeholder'      => $args['placeholder'],
		'show_option_all'  => false,
		'show_option_none' => false
	) );

	return $output;
}
