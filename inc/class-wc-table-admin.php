<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       pangolia.com
 * @since      1.0.0
 *
 * @package    ComparisonTable
 * @subpackage ComparisonTable/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ComparisonTable
 * @subpackage ComparisonTable/admin
 * @author     Atakan Oz <authoeremail.com>
 */

namespace ComparisonTable\Core;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Admin Side
 */
class Backend {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ComparisonTable_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ComparisonTable_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( get_post_type() === 'comparison_table' ) {
			wp_enqueue_style( $this->plugin_name, COMPARISON_TABLE_PLUGIN_URL . 'resources/admin/dist/styles.bundle.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Include Carbon Fields.
	 *
	 * @return void
	 */
	public function add_carbon_fields() {
		require_once COMPARISON_TABLE_PLUGIN_DIR . 'vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * Custom Fields.
	 *
	 * @return void
	 */
	public function comparison_custom_fields() {

		// Winner Comparison Container.
		Container::make( 'post_meta', 'Winner Comparison Table' )
		->where( 'post_type', '=', 'comparison_table' )
		->add_fields(
			array(
				Field::make( 'complex', 'crb_slides', 'Products' )
				->set_layout( 'tabbed-vertical' )
				->add_fields(
					array(
						Field::make( 'image', 'comparison_image', __( 'Image' ) )
						->set_help_text( 'Please use square images. Minimum size: 125 x 125px' ),
						Field::make( 'text', 'comparison_product_name', __( 'Product Name' ) ),
						Field::make( 'checkbox', 'comparison_badge_visibility', __( 'Show Badge?' ) ),
						Field::make( 'radio', 'comparison_badge', __( 'Comparison Badge' ) )
						->set_options(
							array(
								'best_value'     => 'Best Value',
								'best_overall'   => 'Best Overall',
								'premium_choice' => 'Premium Choice',
								'custom'         => 'Custom',
							)
						)
						->set_conditional_logic(
							array(
								array(
									'field' => 'comparison_badge_visibility',
									'value' => true,
								),
							)
						),
						Field::make( 'text', 'comparison_custom_badge_text', __( 'Custom Badge Text' ) )
						->set_conditional_logic(
							array(
								'relation' => 'AND',
								array(
									'field'   => 'comparison_badge',
									'value'   => 'custom',
									'compare' => '=',
								),
							)
						),
						Field::make( 'complex', 'comparison_features', 'Features' )
						->set_layout( 'tabbed-horizontal' )
						->add_fields(
							array(
								Field::make( 'text', 'comparison_feature_name', 'Feature Name' ),
							)
						),
						Field::make( 'text', 'comparison_link', __( 'Product Link' ) )
						->set_attribute( 'type', 'url' ),
					)
				),
			)
		);
	}

	/**
	 * Comparison Table CPT.
	 *
	 * @return void
	 */
	public function comparison_table_post_type() {

		// Create custom post type.
		$args = array(
			'public'              => true,
			'label'               => __( 'Tables', 'textdomain' ),
			'menu_icon'           => 'dashicons-book',
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'rewrite'             => false,
		);

		// Register custom post type.
		register_post_type( 'comparison_table', $args );

		// Remove editor from custom post type.
		remove_post_type_support( 'comparison_table', 'editor' );
	}

	/**
	 * Remove View Button
	 *
	 * @param  mixed $actions
	 * @return void
	 */
	public function remove_view_button( $actions ) {

		// Remove view button in CPT page... just to make sure there is no confusion.
		if ( get_post_type() === 'comparison_table' ) {
			unset( $actions['view'] );
		}
		return $actions;
	}

	/**
	 * Get Post Type
	 *
	 * @return void
	 */
	public function get_post_type() {
		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type ) {
			return $post->post_type;

		} elseif ( $typenow ) {
			return $typenow;

		} elseif ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;

		} elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		return null;
	}

	/**
	 * Shortcode Comment
	 *
	 * @return void
	 */
	public function add_content_before_editor() {

		// Only work at Comparison CPT.
		if ( get_post_type() === 'comparison_table' ) {

			// Create $content.
			$id       = get_the_ID();
			$content  = '';
			$content .= '<div class="comparison-shortcode">';
			$content .= esc_html( '[winner_comparison_table id="' . $id . '"]' );
			$content .= '</div>';

			// Echo $content.
			echo wp_kses_post( $content );

		}

	}

	/**
	 * Set Table Headers.
	 *
	 * @return void
	 */
	public function set_table_headers() {

		// Variables.
		$table_prefix = 'wc-';
		$table_class  = $table_prefix . 'head-col';

		// Table Items.
		$table_items = array(
			'rating'   => array(
				'title' => '',
				'class' => 'rating',
			),
			'image'    => array(
				'title' => 'Image',
				'class' => 'image',
			),
			'product'  => array(
				'title' => 'Product',
				'class' => 'product',
			),
			'features' => array(
				'title' => 'Details',
				'class' => 'features',
			),
			'link'     => array(
				'title' => '',
				'class' => 'link',
			),
		);

		// Templates.
		$table_wrapper         = '<tr class="table-head">%1$s</tr>';
		$table_headers         = '';
		$table_header_template = '<th class="%1$s %2$s">%3$s</th>';

		// Generator.
		foreach ( $table_items as $table_item => $value ) {
			$table_headers .= wp_sprintf( $table_header_template, $table_class, $table_prefix . $value['class'], $value['title'] );
		}

		// Return.
		return wp_sprintf( $table_wrapper, $table_headers );
	}

	/**
	 * WC List Generator.
	 *
	 * @param  mixed $list
	 * @return void
	 */
	public function wc_list_generator( $list = array() ) {

		// Variables.
		$output    = '<td class="comparison-feature_list"><ul>';
		$list_icon = '<svg class="sui-svg" width="15" height="15" preserveAspectRatio="none" viewBox="0 0 1636 1792" style="fill:#3bb1a9" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566c0 25-10 50-28 68l-724 724-136 136c-18 18-43 28-68 28s-50-10-68-28l-136-136-362-362c-18-18-28-43-28-68s10-50 28-68l136-136c18-18 43-28 68-28s50 10 68 28l294 295 656-657c18-18 43-28 68-28s50 10 68 28l136 136c18 18 28 43 28 68z"></path></svg>';

		// Generator.
		foreach ( $list as $list_item ) {
			$output .= '<li>' . $list_icon . ' ' . esc_html( $list_item['comparison_feature_name'] ) . '</li>';
		}

		$output .= '</ul></td>';

		// Return.
		return $output;
	}

	/**
	 * WC Badge Generator.
	 *
	 * @param  mixed $badge_visibility
	 * @param  mixed $badge_selection
	 * @param  mixed $badge_custom
	 * @param  mixed $counter
	 * @return void
	 */
	public function wc_badge_generator( bool $badge_visibility, $badge_selection, $badge_custom, string $counter ) {

		// Variables.
		$badge_image     = '';
		$badge_image_tag = '';
		$badge           = '';
		$badge_text      = '';
		$badge_tag       = '<span class="wc-badge-text">%1$s</span>';

		// Badge Texts.
		if ( $badge_visibility ) {

			if ( $badge_selection !== 'custom' ) {
				switch ( $badge_selection ) {
					case ( $badge_selection === 'best_overall' ):
						$badge_text = 'Best Overall';
						break;
					case ( $badge_selection === 'best_value' ):
						$badge_text = 'Best Value';
						break;
					case ( $badge_selection === 'premium_choice' ):
						$badge_text = 'Premium Choice';
						break;
					default:
						$badge_text = 'Best Overall';
						break;
				}
			} else {
				$badge_text = $badge_custom;
			}

			$badge = wp_sprintf( $badge_tag, $badge_text );
		}

		// Badge Images.
		switch ( $counter ) {
			case ( $counter === '1' ):
				$badge_image = 'https://petkeen.com/wp-content/uploads/2020/09/Gold-medal.webp';
				break;
			case ( $counter === '2' ):
				$badge_image = 'https://petkeen.com/wp-content/uploads/2020/09/Silver-medal.webp';
				break;
			case ( $counter === '3' ):
				$badge_image = 'https://petkeen.com/wp-content/uploads/2020/09/Bronze-medal.webp';
				break;
			default:
				$badge_image = '';
				break;
		}

		// Generator.
		if ( isset( $badge_image ) && ! empty( $badge_image ) ) {
			$badge_image_tag = '<span class="wc-badge-image"><img src="' . $badge_image . '"></span>';
		}

		return '<td class="comparison_badge">' . $badge . $badge_image_tag . '</td>';

	}

	/**
	 * WC Image Generator.
	 *
	 * @param  mixed $attachment_id
	 * @param  mixed $attachment_alt_tag
	 * @return void
	 */
	public function wc_image_generator( $attachment_id, $attachment_alt_tag ) {
		$attachment = wp_get_attachment_image(
			$attachment_id,
			'thumbnail',
			'',
			array(
				'class' => 'comparison_image',
				'alt'   => $attachment_alt_tag,
			)
		);

		return '<td class="comparison_product_image">' . wp_kses_post( $attachment ) . '</td>';
	}

	/**
	 * WC Product Name Generator.
	 *
	 * @param  mixed $product_name
	 * @return void
	 */
	public function wc_product_name_generator( $product_name ) {
		return '<td class="comparison-product_name">' . esc_html( $product_name ) . '</td>';
	}

	/**
	 * WC Product Link Generator.
	 *
	 * @param  mixed $link
	 * @return void
	 */
	public function wc_product_link_generator( $link ) {
		return '<td class="comparison-link"><a class="wc-button" href="' . esc_html( $link ) . '" target="_blank">Check Prices</a></td>';
	}


	/**
	 * Winners Comparison Table Shortcode.
	 * [winner_comparison_table]
	 *
	 * @return void
	 */
	public function winners_shortcode() {

		// Anonymus function, because why not?.
		add_shortcode(
			'winner_comparison_table',
			function ( $atts ) {

				// Shortcode attributes.
				$atts = shortcode_atts(
					array(
						'id' => '',
					),
					$atts
				);

				// Variables.
				$id  = $atts['id'];
				$out = '';

				// Arguments.
				$args = array(
					'post_type'      => 'comparison_table',
					'id'             => $id,
					'posts_per_page' => '1',
				);

				// Query.
				$comparison_list = new \WP_Query( $args );
				$table_headings  = $this->set_table_headers();
				$wrapper         = '<div class="wc-table"><table id="ct-' . $id . '" class="comparison-table"><tbody>' . $table_headings . ' %1$s</tbody></table></div>';

				if ( $comparison_list->have_posts() ) {

					while ( $comparison_list->have_posts() ) {

						$counter = '';

						$comparison_list->the_post();
						$lists = carbon_get_post_meta( get_the_ID(), 'crb_slides' );

						foreach ( $lists as $list ) {

							$badge_visibility = $list['comparison_badge_visibility'];
							$badge_class      = $list['comparison_badge_visibility'] ? 'has-badge' : '';
							$badge_selection  = $list['comparison_badge'];
							$badge_custom     = $list['comparison_custom_badge_text'];
							$product_name     = $list['comparison_product_name'];
							$product_features = $list['comparison_features'];
							$product_link     = $list['comparison_link'];
							$attachment       = $list['comparison_image'];
							$image_alt_tag    = $list['comparison_product_name'] . ' Image';
							$counter++;

							$out .= '<tr class="comparison-row ' . $badge_class . '">';
							$out .= $this->wc_badge_generator( $badge_visibility, $badge_selection, $badge_custom, $counter );
							$out .= $this->wc_image_generator( $attachment, $image_alt_tag );
							$out .= $this->wc_product_name_generator( $product_name );
							$out .= $this->wc_list_generator( $product_features );
							$out .= $this->wc_product_link_generator( $product_link );
							$out .= '</tr>';
						}

						$counter = '';
					}
				}

				return wp_sprintf( $wrapper, $out );
			}
		);
	}
}
