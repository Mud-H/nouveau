<?php
namespace NV;

use \NV\HtmlGen, \NV\Html;

/**
 * This allows WordPress to generate Foundation 2.0 menus since the menu HTML
 * generated by WordPress is not directly compatible with Foundation. This class 
 * is a modified copy of the Walker_Nav_Menu class found in \wp-includes\nav-menu-template.php 
 * with the necessary changes.
 * 
 * This walker class can be used by specifying the 'walker' argument in the
 * arguments array of wp_nav_menu(). Example usage:
 * 
 * {{{
 * wp_nav_menu( array(
 *      'theme_location'    => 'primary',
 *      'items_wrap'        => '<ul id="%1$s" class="nav-bar %2$s">%3$s</ul>',
 *      'walker'            => new \NV\MenuWalker,
 * ) );
 * }}}
 * 
 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
 * @link http://wikiduh.com/1541/custom-nav-menu-walker-function-to-add-classes
 * @see Walker_Nav_Menu
 * @since Nouveau 1.0 
 */
class FoundationMenuWalker extends \Walker {

    /**
     * @see Walker::$tree_type
         * 
     * @var string
         * 
         * @since Nouveau 1.0
     */
    var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

    /**
     * @see Walker::$db_fields
         * 
     * @todo Decouple this.
     * @var array
         * @since Nouveau 1.0
     */
    var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

    /**
     * @see Walker::start_lvl()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
         * 
     * @since Nouveau 1.0
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    /**
     * @see Walker::end_lvl()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
         * 
         * @since Nouveau 1.0
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * @see Walker::start_el()
     *
     * @global \WP_Query $wp_query
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param int $current_page Menu item ID.
     * @param object $args
       * 
       * @since Nouveau 1.0
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;

                //Pre-pad row with tabs
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

                //Begin building class names with an empty string
        $li_class_names = '';
                $value = '';

                //Start an array of of classes
        $li_classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $li_classes[] = 'menu-item-' . $item->ID;

                //Start an array 
        $li_class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $li_classes ), $item, $args ) );
        $li_class_names = $li_class_names ? ' class="' . esc_attr( $li_class_names ) . '"' : '';

                //Build id attribute
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

                //Build the li opening tag
        $output .= $indent . '<li' . $id . $value . $li_class_names .'>';

                //Generate attributes for 
                $link_attributes = HtmlGen::atts( array(
                        'title'     => empty( $item->attr_title ) ? '' : esc_attr( $item->attr_title ),
                        'target'    => empty( $item->target )     ? '' : esc_attr( $item->target     ),
                        'rel'       => empty( $item->xfn )        ? '' : esc_attr( $item->xfn        ),
                        'href'      => empty( $item->url )        ? '' : esc_attr( $item->url        ),
                        'class'     => empty( $item->class )      ? array('main') : $item->class,
                ) );

                //Build output and link
        $item_output = $args->before;
        $item_output .= '<a '. $link_attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * @see Walker::end_el()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Page data object. Not used.
     * @param int $depth Depth of page. Not Used.
         * 
         * @since Nouveau 1.0
     */
    function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }

}