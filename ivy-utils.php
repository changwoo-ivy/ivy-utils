<?php
/**
 * Ivynet WordPress Utility
 *
 * @Author: Changwoo Nam
 */

if ( ! defined( 'IVYNET_UTILS_VERSION' ) ) {
	define( 'IVYNET_UTILS_VERSION', '1.0.0' );
}


if ( ! function_exists( 'ivy_check_abspath' ) ) {
	/**
	 * Shorthand for access check
	 *
	 * @param string $msg
	 */
	function ivy_check_abspath( $msg = '' ) {
		if ( ! defined( 'ABSPATH' ) ) {
			die( $msg );
		}
	}
}


if ( ! function_exists( 'ivy_from_assoc' ) ) {
	/**
	 * Extracts a value from given associative array.
	 *
	 * @param array        $assoc_var
	 * @param string       $key_name
	 * @param string|array $sanitize
	 * @param string       $default
	 *
	 * @return mixed|string
	 */
	function ivy_from_assoc( array &$assoc_var, $key_name, $sanitize = '', $default = '' ) {

		$value = $default;

		if ( isset( $assoc_var[ $key_name ] ) ) {
			$value = $assoc_var[ $key_name ];
		}

		if ( is_array( $sanitize ) ) {
			foreach ( $sanitize as $sf ) {
				if ( is_callable( $sf ) ) {
					$value = call_user_func( $sf, $value );
				}
			}
		} else {
			$value = call_user_func( $sanitize, $value );
		}

		return $value;
	}
}


if ( ! function_exists( 'ivy_from_GET' ) ) {
	function ivy_from_GET( $key_name, $sanitize = '', $default = '' ) {
		return ivy_from_assoc( $_GET, $key_name, $sanitize, $default );
	}
}


if ( ! function_exists( 'ivy_from_POST' ) ) {
	function ivy_from_POST( $key_name, $sanitize = '', $default = '' ) {
		return ivy_from_assoc( $_POST, $key_name, $sanitize, $default );
	}
}


if ( ! function_exists( 'ivy_from_REQUEST' ) ) {
	function ivy_from_REQUEST( $key_name, $sanitize = '', $default = '' ) {
		return ivy_from_assoc( $_REQUEST, $key_name, $sanitize, $default );
	}
}


if ( ! function_exists( 'ivy_verify_nonce' ) ) {
	/**
	 * Shorthand for verifying nonce values
	 *
	 * @param mixed  $nonce_value  nonce values from GET, POST, or else.
	 * @param string $nonce_action action name that you defined
	 * @param string $fail_message override this for die message
	 */
	function ivy_verify_nonce( $nonce_value, $nonce_action, $fail_message = 'Nonce verification failed' ) {
		if ( ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
			wp_die( $fail_message );
		}
	}
}


if ( ! function_exists( 'ivy_enqueue_script' ) ) {
	/**
	 * Shorthand for registering, localizing, and enqueuing scripts
	 *
	 * @param string $handle      script's handle name
	 * @param string $asset_path  script path
	 * @param array  $depends     depending plugins
	 * @param null   $ver         version. FALSE for skipping version info
	 * @param bool   $in_footer   place it in the footer?
	 * @param string $object_name variable name in the javascript for localization
	 * @param array  $l10n        any entries for script localization
	 */
	function ivy_enqueue_script(
		$handle,
		$asset_path,
		$depends = array(),
		$ver = NULL,
		$in_footer = FALSE,
		$object_name = '',
		$l10n = array()
	) {
		wp_register_script( $handle, $asset_path, $depends, $ver, $in_footer );

		if ( ! empty( $object_name ) && ! empty( $l10n ) ) {
			wp_localize_script( $handle, $object_name, $l10n );
		}

		wp_enqueue_script( $handle );
	}
}


if ( ! function_exists( 'ivy_get_template' ) ) {
	/**
	 * Simple template loader
	 *
	 * @param string $template_path full path to template location
	 * @param array  $args          context
	 */
	function ivy_get_template( $template_path, array $args = array() ) {
		if ( ! is_string( $template_path ) || empty( $template_path ) ) {
			return;
		}

		if ( ! file_exists( $template_path ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_path ), '2.1' );
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		/** @noinspection PhpIncludeInspection */
		include( $template_path );
	}
}


if ( ! function_exists( 'ivy_get_template_html' ) ) {
	/**
	 * returning output of a template as string
	 *
	 * @param       $template_path
	 * @param array $args
	 *
	 * @return string
	 */
	function ivy_get_template_html( $template_path, array $args = array() ) {
		ob_start();
		ivy_get_template( $template_path, $args );

		return ob_get_clean();
	}
}


if ( ! function_exists( 'ivy_dump' ) ) {
	/**
	 * Simple and handy object dump
	 *
	 * @param $obj
	 */
	function ivy_dump( $obj ) {
		echo '<p><pre>' . print_r( $obj, TRUE ) . '</pre></p>';
	}
}


if ( ! class_exists( 'CustomPostMetaValueHelper' ) ) {
	/**
	 * Class CustomPostMetaValueHelper
	 *
	 * Easy retrieving from submitted form data.
	 */
	class CustomPostMetaValueHelper {

		private $metadata = array();

		public function add_meta_info( $key, $sanitizer = '', $default = '' ) {
			$this->metadata[ $key ] = array( 'sanitizer' => $sanitizer, 'default' => $default );
		}

		public function extract_from_assoc( &$assoc_var, $as_obj = FALSE ) {

			$return = array();

			foreach ( $this->metadata as $key => $item ) {
				$return[ $key ] = ivy_from_assoc( $assoc_var, $key, $item['sanitizer'], $item['default'] );
			}

			if ( $as_obj ) {
				return (object) $return;
			}

			return $return;
		}
	}
}