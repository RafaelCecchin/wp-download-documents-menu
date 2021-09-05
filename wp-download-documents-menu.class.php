<?php

    class wp_download_documents_menu {
        
        function __construct() {
            add_action( 'wp_nav_menu_item_custom_fields', array($this, 'my_wp_nav_menu_item_custom_fields'), 10, 4 );
            add_action( 'wp_update_nav_menu_item', array($this, 'my_wp_update_nav_menu_item'), 10, 2 );
            add_filter( 'nav_menu_link_attributes', array($this, 'my_nav_menu_link_attributes'), 10, 4 );
        }

        function my_wp_nav_menu_item_custom_fields( $item_id, $item, $depth, $args ) {
            if ($item->type == 'custom') {
                $is_download = (bool) get_post_meta( $item_id, 'is_download', true );
            
                wp_nonce_field( 'nav_menu_edit', 'nav_menu_is_download' );
                ?>
                <div>
                    <input
                        type="checkbox"
                        class="nav-menu-is-download"
                        name="nav-menu-is-download[<?php echo esc_attr( $item_id ); ?>]"
                        id="nav-menu-is-download-for-<?php echo esc_attr( $item_id ); ?>"
                        <?php checked( '1', $is_download ); ?>
                        value="1">
                    <label for="nav-menu-is-download-for-<?php echo esc_attr( $item_id ); ?>">
                        <?php _e( 'Download', 'text-domain'); ?>
                    </label>
                </div>
                <?php
            }
        }

        function my_wp_update_nav_menu_item( $menu_id, $menu_item_db_id ) {
            if ( ! isset( $_POST['nav_menu_is_download'] ) || ! wp_verify_nonce( $_POST['nav_menu_is_download'], 'nav_menu_edit' ) ) {
                return;
            }
            
            $is_download = ( ! empty( $_POST['nav-menu-is-download'][ $menu_item_db_id ] ) && '1' === $_POST['nav-menu-is-download'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, 'is_download', $is_download );
        }

        function my_nav_menu_link_attributes( $attrs, $item, $args = array(), $depth = 0 ) {
            $is_download = (bool) get_post_meta( $item->ID, 'is_download', true );
            
            if ( $is_download ) {
                $attrs['download'] = $item->title;
            }
            return $attrs;
        }
    }