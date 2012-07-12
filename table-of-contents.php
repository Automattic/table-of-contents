<?php
/**
 * Plugin Name: Table of Contents
 * Description: Adds a table of contents to your pages based on h3 and h4 tags. Useful for documention-centric sites.
 * Author: Automattic
 *
 * License: GPL v2
 */

if ( ! class_exists( 'Table_Of_Contents' ) ):

class Table_Of_Contents {
	function init() {
		add_action( 'template_redirect', array( __CLASS__, 'load_filters' ) );
	}

	function load_filters() {
		if ( is_page() ) {
			add_filter( 'the_content', array( __CLASS__, 'add_overview_h3' ) );
			add_filter( 'the_content', array( __CLASS__, 'add_toc' ) );
		}
	}

	function add_toc( $content ) {
		$toc = '';
		$h3s = self::get_tags( 'h3', $content );
		$h4s = self::get_tags( 'h4', $content );
		$items = $h3s + $h4s;

		$content = self::add_ids_and_jumpto_links( 'h3', $content );
		$content = self::add_ids_and_jumpto_links( 'h4', $content );

		if ( $items ) {
			$toc .= '<div class="vip-lobby-toc">';
			$toc .= '<h3>Contents</h3><ul class="items">';
			foreach ($items as $item) {
				$toc .= '<li><a href="#' . sanitize_title_with_dashes($item[2])  . '">' . $item[2]  . '</a></li>';
			}
			$toc .= '</ul>';
			$toc .= '</div>';
		}

		return $toc . $content;
	}

	function add_overview_h3( $content ) {
		$h3s = self::get_tags( 'h3', $content );
		if ( ! empty( $h3s ) )
			$content = "<h3>Overview</h3>\n" . $content;
		return $content;
	}

	private function add_ids_and_jumpto_links( $tag, $content ) {
		$items = self::get_tags( $tag, $content );
		$first = true;

		foreach ($items as $item) {
			$replacement = '';
			$matches[] = $item[0];
			$id = sanitize_title_with_dashes($item[2]);

			if ( ! $first ) {
				$replacement .= '<p class="toc-jump"><a href="#content">&uarr; Top &uarr;</a></p>';
			} else {
				$first = false;
			}

			$replacement .= sprintf( '<%1$s id="%2$s">%3$s <a href="#%2$s" class="anchor">#</a></%1$s>', $tag, $id, $item[2] );
			$replacements[] = $replacement;
		}

		$content = str_replace( $matches, $replacements, $content );

		return $content;
	}

	private function get_tags( $tag, $content = '' ) {
		if ( empty( $content ) )
			$content = get_the_content();
		preg_match_all( "/(<{$tag}>)(.*)(<\/{$tag}>)/", $content, $matches, PREG_SET_ORDER );
		return $matches;
	}
}

Table_Of_Contents::init();

endif;
