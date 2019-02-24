<?php
/*
Plugin Name: StoreFront TopBar with flexible column widgets
Plugin URI: https://bogaczek.com
Description: This plugin creates TopBar that is works exactly (but independent) like a Footer bar. For WooCommerce StoreFront theme only.
Version: 0.7
Author: Black Sun
Author URI: https://bogaczek.com
Text Domain: storefront-topbar-with-flexible-column-widgets
*/
defined('ABSPATH') or die();

// Enqueuing basic styles for plugin
function dexter_topbar_widgets_styles() {
	wp_enqueue_style( 'dexter-topbar-widgets-style', plugins_url( 'assets/css/style.css', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'dexter_topbar_widgets_styles', 666 );



// Register widget area @link https://codex.wordpress.org/Function_Reference/register_sidebar
function dexter_topbar_widgets_init() {

	$rows    = intval( apply_filters( 'storefront_topbar_widget_rows', 1 ) );
	$regions = intval( apply_filters( 'storefront_topbar_widget_columns', 4 ) );

	for ( $row = 1; $row <= $rows; $row++ ) {
		for ( $region = 1; $region <= $regions; $region++ ) {
			$topbar_n = $region + $regions * ( $row - 1 ); // Defines topbar sidebar ID.
			$topbar   = sprintf( 'topbar_%d', $topbar_n );

			if ( 1 === $rows ) {
				/* translators: 1: column number */
				$topbar_region_name = sprintf( __( 'TopBar Column %1$d', 'storefront' ), $region );

				/* translators: 1: column number */
				$topbar_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the TopBar.', 'storefront' ), $region );
			} else {
				/* translators: 1: row number, 2: column number */
				$topbar_region_name = sprintf( __( 'TopBar Row %1$d - Column %2$d', 'storefront' ), $row, $region );

				/* translators: 1: column number, 2: row number */
				$topbar_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of TopBar row %2$d.', 'storefront' ), $region, $row );
			}

			$sidebar_args[ $topbar ] = array(
				'name'        => $topbar_region_name,
				'id'          => sprintf( 'topbar-%d', $topbar_n ),
				'description' => $topbar_region_description,
			);
		}
	}

	$sidebar_args = apply_filters( 'storefront_sidebar_args', $sidebar_args );

	foreach ( $sidebar_args as $sidebar => $args ) {
		$widget_tags = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',
		);

		/**
		* Dynamically generated filter hooks. Allow changing widget wrapper and title tags.
		* List: 'storefront_header_widget_tags', 'storefront_sidebar_widget_tags', 'storefront_topbar_1_widget_tags', 'storefront_topbar_2_widget_tags', 'storefront_topbar_3_widget_tags', 'storefront_topbar_4_widget_tags'
		*/
		$filter_hook = sprintf( 'storefront_%s_widget_tags', $sidebar );
		$widget_tags = apply_filters( $filter_hook, $widget_tags );

		if ( is_array( $widget_tags ) ) {
			register_sidebar( $args + $widget_tags );
		}
	}
}
add_action( 'widgets_init', 'dexter_topbar_widgets_init' );

//Display the topbar widget regions.
function dexter_storefront_topbar_widgets() {
	$rows    = intval( apply_filters( 'storefront_topbar_widget_rows', 1 ) );
	$regions = intval( apply_filters( 'storefront_topbar_widget_columns', 4 ) );

	for ( $row = 1; $row <= $rows; $row++ ) :

	// Defines the number of active columns in this topbar row.
	for ( $region = $regions; 0 < $region; $region-- ) {
		if ( is_active_sidebar( 'topbar-' . esc_attr( $region + $regions * ( $row - 1 ) ) ) ) {
			$columns = $region;
			break;
		}
	}

	if ( isset( $columns ) ) :
?>
<div class="topbar-widgets-container">
	<div class=<?php echo '"col-full topbar-widgets row-' . esc_attr( $row ) . ' col-' . esc_attr( $columns ) . ' fix"'; ?>>
	<?php
	for ( $column = 1; $column <= $columns; $column++ ) :
	$topbar_n = $column + $regions * ( $row - 1 );

	if ( is_active_sidebar( 'topbar-' . esc_attr( $topbar_n ) ) ) :
	?>
		<div class="block topbar-widget-<?php echo esc_attr( $column ); ?>">
		<?php dynamic_sidebar( 'topbar-' . esc_attr( $topbar_n ) ); ?>
		</div>
	<?php
	endif;
	endfor;
	?>
	</div><!-- .topbar-widgets.row-<?php echo esc_attr( $row ); ?> -->
</div>
<?php
	unset( $columns );
	endif;
	endfor;
}
add_action( 'storefront_before_header', 'dexter_storefront_topbar_widgets' );
?>