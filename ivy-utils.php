<?php
/**
 * Ivynet WordPress Utility
 *
 * Changwoo Nam
 */

/**
 * Extracts a value from given associative array.
 *
 * @param array         $assoc_var
 * @param string        $key_name
 * @param string|array  $sanitize
 * @param string        $default
 *
 * @return mixed|string
 */
function ivy_from_assoc( array &$assoc_var, $key_name, $sanitize = '', $default = '' ) {

	$value = $default;

	if( isset( $assoc_var[ $key_name ] ) ) {
		$value = $assoc_var[ $key_name ];
	}

	if( is_array( $sanitize ) ) {
		foreach( $sanitize as $sf ) {
			if( is_callable( $sf ) ) {
				$value = call_user_func( $sf, $value );
			}
		}
	} else {
		$value = call_user_func( $sanitize, $value );
	}

	return $value;
}


function ivy_from_GET( $key_name, $sanitize = '', $default = '' ) {
	return ivy_from_assoc( $_GET, $key_name, $sanitize, $default );
}


function ivy_from_POST( $key_name, $sanitize = '', $default = '') {
	return ivy_from_assoc( $_POST, $key_name, $sanitize, $default );
}


function ivy_from_REQUEST( $key_name, $sanitize = '', $default = '' ) {
	return ivy_from_assoc( $_REQUEST, $key_name, $sanitize, $default );
}


function ivy_verify_nonce( $nonce_value, $nonce_action, $fail_message = 'Nonce verification failed' ) {
	if( ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
		wp_die( $fail_message );
	}
}


function ivy_enqueue_script(
	$handle,
	$asset_path,
	$depends = array(),
	$ver = NULL,
	$in_footer = FALSE,
	$object_name = '',
	$l10n = array()
) {
	wp_register_script( $handle, $asset_path, $depends, $ver, $in_footer);

	if( !empty( $object_name ) && !empty( $l10n ) ) {
		wp_localize_script( $handle, $object_name, $l10n );
	}

	wp_enqueue_script( $handle );
}


function ivy_get_template( $template_path, array $args = array() ) {
	if( ! is_string( $template_path ) || empty( $template_path ) ) {
		return;
	}

	if( ! file_exists( $template_path ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf(  '<code>%s</code> does not exist.', $template_path ), '2.1' );
	}

	if( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	/** @noinspection PhpIncludeInspection */
	include( $template_path );
}


function ivy_get_template_html( $template_path, array $args = array() ) {
	ob_start();
	ivy_get_template( $template_path, $args );
	return ob_get_clean();
}


function ivy_dump( $obj ) {
	echo '<p><pre>' . print_r( $obj, TRUE ) . '</pre></p>';
}


class CustomPostMetaValueHelper {

	private $metadata = array();

	public function add_meta_info( $key, $sanitizer = '', $default = '' ) {
		$this->metadata[ $key ]  = array( 'sanitizer' => $sanitizer, 'default' => $default );
	}

	public function extract_from_assoc( &$assoc_var, $as_obj = FALSE ) {

		$return = array();

		foreach( $this->metadata as $key => $item ) {
			$return[ $key ] = ivy_from_assoc( $assoc_var, $key, $item['sanitizer'], $item['default'] );
		}

		if( $as_obj ) {
			return (object)$return;
		}

		return $return;
	}
}
