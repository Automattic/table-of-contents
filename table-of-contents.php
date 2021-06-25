<?php
/**
 * Plugin Name: Table of Contents
 * Description: Adds a table of contents to your pages based on h3 and h4 tags. Useful for documention-centric sites.
 * Author: Automattic
 * Author URI: http://automattic.com/
 * Version: 0.5.1
 * License: GPL v2
 *
 * @package table-of-contents
 */

if ( ! class_exists( 'Table_Of_Contents' ) ) {
	require_once __DIR__ . '/class-table-of-contents.php';
	Table_Of_Contents::init();
}
