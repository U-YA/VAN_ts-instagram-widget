<?php

Class null_instagram_widget extends WP_Widget {

  function null_instagram_widget() {
        global $wpiwdomain;
        $this->wpiwdomain = $wpiwdomain;
        $widget_ops = array('classname' => 'null-instagram-feed', 'description' => __('Displays your latest Instagram photos', $this->wpiwdomain) );
        parent::__construct('null-instagram-feed', __(THEMESTUDIO_THEME_NAME.' Footer Instagram', $this->wpiwdomain), $widget_ops);
    }

    function widget( $args, $instance ) {

        $title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
        $username = empty( $instance['username'] ) ? '' : $instance['username'];
        $limit = empty( $instance['number'] ) ? 9 : $instance['number'];
        $size = empty( $instance['size'] ) ? 'large' : $instance['size'];
        $target = empty( $instance['target'] ) ? '_self' : $instance['target'];
        $link = empty( $instance['link'] ) ? '' : $instance['link'];

        echo $args['before_widget'];

        if ( ! empty( $title ) ) { echo $before_title . $title . $after_title; };

        do_action( 'wpiw_before_widget', $instance );

        if ( '' !== $username ) {

            $media_array = $this->ts_mr_instagram( $username );

            if ( is_wp_error( $media_array ) ) {

                echo $media_array->get_error_message();

            } else {

                // filter for images only?
                if ( $images_only = apply_filters( 'wpiw_images_only', false ) ) {
                    $media_array = array_filter( $media_array, array( $this, 'images_only' ) );
                }
                
                ?>



                <div class="footer-item footer-twitter footer-photo text-center">
                <i class="icon-twitter fa fa-camera"></i>
                <span class="twitter-title"><a href="//instagram.com/<?php echo trim($username); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>">#<?php echo $username;?></a></span>
                <div class="hr"></div>
                <div id="owl-photo" class="photo-content owl-photo">


                <?php
                foreach( $media_array as $item ) {
                    // copy the else line into a new file (parts/wp-instagram-widget.php) within your theme and customise accordingly.
                    if ( locate_template( $template_part ) !== '' ) {
                        include locate_template( $template_part );
                    } else {
                        echo '<div class="photo-item"><a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"><img src="'. esc_url($item['thumbnail']) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'"/></a></div>';
                    }
                }
                ?>
                </div>
                <span id="prev-photo" class="prev-next"><i class="fa fa-caret-left"></i></span>
                <span id="next-photo" class="prev-next"><i class="fa fa-caret-right"></i></span>
                </div>
                <?php
            }
        }

        $linkclass = apply_filters( 'wpiw_link_class', 'clear' );
        $linkaclass = apply_filters( 'wpiw_linka_class', '' );

        switch ( substr( $username, 0, 1 ) ) {
            case '#':
                $url = '//instagram.com/explore/tags/' . str_replace( '#', '', $username );
                break;

            default:
                $url = '//instagram.com/' . str_replace( '@', '', $username );
                break;
        }

        if ( '' !== $link ) {
            ?><p class="clear"><a href="//instagram.com/<?php echo trim($username); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"><?php echo $link; ?></a></p><?php
        }

        do_action( 'wpiw_after_widget', $instance );

        echo $args['after_widget'];
    }

  function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => __('Instagram', $this->wpiwdomain), 'username' => '', 'link' => __('Follow Us', $this->wpiwdomain), 'number' => 9, 'size' => 'thumbnail', 'target' => '_self') );
        $title = esc_attr($instance['title']);
        $username = esc_attr($instance['username']);
        $number = absint($instance['number']);
        $size = esc_attr($instance['size']);
        $target = esc_attr($instance['target']);
        $link = esc_attr($instance['link']);
        ?>        
        <p><label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username', $this->wpiwdomain); ?>: <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of photos', $this->wpiwdomain); ?>: <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Photo size', $this->wpiwdomain); ?>:</label>
            <select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" class="widefat">
                <option value="thumbnail" <?php selected('thumbnail', $size) ?>><?php _e('Thumbnail', $this->wpiwdomain); ?></option>
                <option value="large" <?php selected('large', $size) ?>><?php _e('Large', $this->wpiwdomain); ?></option>
            </select>
        </p>
        <p><label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Open links in', $this->wpiwdomain); ?>:</label>
            <select id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" class="widefat">
                <option value="_self" <?php selected('_self', $target) ?>><?php _e('Current window (_self)', $this->wpiwdomain); ?></option>
                <option value="_blank" <?php selected('_blank', $target) ?>><?php _e('New window (_blank)', $this->wpiwdomain); ?></option>
            </select>
        </p>
        <p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link text', $this->wpiwdomain); ?>: <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" /></label></p>
        <?php

    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['username'] = trim( strip_tags( $new_instance['username'] ) );
        $instance['number'] = ! absint( $new_instance['number'] ) ? 9 : $new_instance['number'];
        $instance['size'] = ( ( 'thumbnail' === $new_instance['size'] || 'large' === $new_instance['size'] || 'small' === $new_instance['size'] || 'original' === $new_instance['size'] ) ? $new_instance['size'] : 'large' );
        $instance['target'] = ( ( '_self' === $new_instance['target'] || '_blank' === $new_instance['target'] ) ? $new_instance['target'] : '_self' );
        $instance['link'] = strip_tags( $new_instance['link'] );
        return $instance;
    }

    // based on https://gist.github.com/cosmocatalano/4544576.
    function ts_mr_instagram( $username ) {

        $username = trim( strtolower( $username ) );

        switch ( substr( $username, 0, 1 ) ) {
            case '#':
                $url              = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
                $transient_prefix = 'h';
                break;

            default:
                $url              = 'https://instagram.com/' . str_replace( '@', '', $username );
                $transient_prefix = 'u';
                break;
        }

        if ( false === ( $instagram = get_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ) ) ) ) {

            $remote = wp_remote_get( $url );

            if ( is_wp_error( $remote ) ) {
                return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'van' ) );
            }

            if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
                return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'van' ) );
            }

            $shards      = explode( 'window._sharedData = ', $remote['body'] );
            $insta_json  = explode( ';</script>', $shards[1] );
            $insta_array = json_decode( $insta_json[0], true );

            if ( ! $insta_array ) {
                return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'van' ) );
            }

            if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
                $images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
            } elseif ( isset( $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
                $images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
            } else {
                return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
            }

            if ( ! is_array( $images ) ) {
                return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
            }

            $instagram = array();

            foreach ( $images as $image ) {
                if ( true === $image['node']['is_video'] ) {
                    $type = 'video';
                } else {
                    $type = 'image';
                }

                $caption = __( 'Instagram Image', 'wp-instagram-widget' );
                if ( ! empty( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
                    $caption = wp_kses( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array() );
                }

                $instagram[] = array(
                    'description' => $caption,
                    'link'        => trailingslashit( '//instagram.com/p/' . $image['node']['shortcode'] ),
                    'time'        => $image['node']['taken_at_timestamp'],
                    'comments'    => $image['node']['edge_media_to_comment']['count'],
                    'likes'       => $image['node']['edge_liked_by']['count'],
                    'thumbnail'   => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src'] ),
                    'small'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src'] ),
                    'large'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][4]['src'] ),
                    'original'    => preg_replace( '/^https?\:/i', '', $image['node']['display_url'] ),
                    'type'        => $type,
                );
            } // End foreach().

            // do not set an empty transient - should help catch private or empty accounts.
            if ( ! empty( $instagram ) ) {
                $instagram = base64_encode( serialize( $instagram ) );
                set_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS * 2 ) );
            }
        }

        if ( ! empty( $instagram ) ) {

            return unserialize( base64_decode( $instagram ) );

        } else {

            return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'van' ) );

        }
    }

    function images_only( $media_item ) {

        if ( 'image' === $media_item['type'] ) {
            return true;
        }

        return false;
    }
}

function wpiw_widget() {
    register_widget('null_instagram_widget');
}
add_action('widgets_init', 'wpiw_widget');
?>