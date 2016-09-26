<?php
	/**
	 * The template for the panel header area.
	 *
	 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
	 *
	 * @author 	Redux Framework
	 * @package 	ReduxFramework/Templates
	 * @version     3.4.4
	 */

$tip_title  = __('Developer Mode Enabled', 'redux-framework');

if ($this->parent->dev_mode_forced) {
    $is_debug       = false;
    $is_localhost   = false;
    
    $debug_bit = '';
    if (Redux_Helpers::isWpDebug ()) {
        $is_debug = true;
        $debug_bit = __('WP_DEBUG is enabled', 'redux-framework');
    }
    
    $localhost_bit = '';
    if (Redux_Helpers::isLocalHost ()) {
        $is_localhost = true;
        $localhost_bit = __('you are working in a localhost environment', 'redux-framework');
    }
    
    $conjunction_bit = '';
    if ($is_localhost && $is_debug) {
        $conjunction_bit = ' ' . __('and', 'redux-framework') . ' ';
    }
    
    $tip_msg    = __('Redux has enabled developer mode because', 'redux-framework') . ' ' . $debug_bit . $conjunction_bit . $localhost_bit . '.';
} else {
    $tip_msg    = __('If you are not a developer, your theme/plugin author shipped with developer mode enabled. Contact them directly to fix it.', 'redux-framework');
}

?>