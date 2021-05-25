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

if ( ! class_exists( 'Table_Of_Contents' ) ) :

	/**
	 * Table of Contents
	 */
	class Table_Of_Contents {

		/**
		 * Kick off the class.
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'template_redirect', array( __CLASS__, 'load_filters' ) );
		}

		/**
		 * Load the filters.
		 *
		 * @return void
		 */
		public static function load_filters() {
			if ( is_page() ) {
				add_filter( 'the_content', array( __CLASS__, 'add_overview_h3' ) );
				add_filter( 'the_content', array( __CLASS__, 'add_toc' ) );
			}
		}

		/**
		 * Add the TOC to the top of the page.
		 *
		 * @param string $content Page content.
		 *
		 * @return string
		 */
		public static function add_toc( $content = '' ) {
			$toc   = '';
			$h3s   = self::get_tags( 'h3', $content );
			$h4s   = self::get_tags( 'h4', $content );
			$items = $h3s + $h4s;

			$content = self::add_ids_and_jumpto_links( 'h3', $content );
			$content = self::add_ids_and_jumpto_links( 'h4', $content );

			if ( $items ) {
				$toc .= '<div class="vip-lobby-toc">';
				$toc .= '<h3>Contents</h3><ul class="items">';
				foreach ( $items as $item ) {
					$toc .= '<li><a href="#' . sanitize_title_with_dashes( $item[2] ) . '">' . $item[2] . '</a></li>';
				}
				$toc .= '</ul>';
				$toc .= '</div>';
			}

			return $toc . $content;
		}

		/**
		 * Add an overview to the top of the page.
		 *
		 * @param string $content Page content.
		 *
		 * @return string
		 */
		public static function add_overview_h3( $content ) {
			$h3s = self::get_tags( 'h3', $content );
			if ( ! empty( $h3s ) ) {
				$content = "<h3>Overview</h3>\n" . $content;
			}
			return $content;
		}

		/**
		 * Add IDs and anchor links to the headings.
		 *
		 * @param string $tag     Tag to add anchor to.
		 * @param string $content Post content.
		 *
		 * @return string
		 */
		private static function add_ids_and_jumpto_links( $tag, $content ) {
			$items = self::get_tags( $tag, $content );
			$first = true;

			$matches = $replacements = [];

			foreach ( $items as $item ) {
				$replacement = '';
				$matches[]   = $item[0];
				$id          = sanitize_title_with_dashes( $item[2] );

				if ( ! $first ) {
					$replacement .= '<p class="toc-jump"><a href="#content">&uarr; Top &uarr;</a></p>';
				} else {
					$first = false;
				}

				$replacement   .= sprintf( '<%1$s id="%2$s">%3$s <a href="#%2$s" class="anchor">#</a></%1$s>', $tag, $id, $item[2] );
				$replacements[] = $replacement;
			}

			$content = str_replace( $matches, $replacements, $content );

			return $content;
		}

		/**
		 * Get the tags from post_content
		 *
		 * @param string $tag     Tag to search for.
		 * @param string $content Post content.
		 *
		 * @return array
		 */
		private static function get_tags( $tag, $content = '' ) {
			if ( empty( $content ) ) {
				$content = get_the_content();
			}
			preg_match_all( "/(<{$tag}>)(.*)(<\/{$tag}>)/", $content, $matches, PREG_SET_ORDER );
			return $matches;
		}
	}

	Table_Of_Contents::init();

endif;
