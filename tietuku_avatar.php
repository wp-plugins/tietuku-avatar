<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Plugin Name: Tietuku Avatar for WordPress
 * Description: This WordPress plugin let you use Tietuku Avatar service to replace Gravatar
 * Version: 0.2.0
 * Author: qakcn
 * Author URI: http://tsukkomi.org
 * License: GPLv3
 */

/*  Copyright 2015  qakcn  (email : qakcnyn@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

add_filter('get_avatar', 'tietuku_avatar', 1, 5);

function tietuku_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( $user && is_object( $user ) ) {

        if ( $user->data->ID == '1' ) {
            $avatar = 'http://avatar.tietuku.com/avatar/'.md5(strtolower(trim($user->email))).'?size='.$size;
            $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }

    }

    return $avatar;
}

/* Replace Fonts and Scripts Hosted by Google with USTC mirror, for Chinese users */
function replace_google_cdn() {
    global $wp_scripts,$wp_styles;

    foreach($wp_styles->registered as $reg) {
        if(preg_match('/fonts\.googleapis\.com/', $reg->src)) {
            $src = str_replace('fonts.googleapis.com','fonts.lug.ustc.edu.cn',$reg->src);
            $handle = $reg->handle;
            $deps = $reg->deps;
            $ver = $reg->ver;
            $media = $reg->args;
            wp_deregister_style($handle);
            wp_register_style($handle,$src,$deps,$ver,$media);
        }
    }
    foreach($wp_scripts->registered as $reg) {
        if(preg_match('/ajax\.googleapis\.com/', $reg->src)) {
            $src = str_replace('ajax.googleapis.com','ajax.lug.ustc.edu.cn',$reg->src);
            $handle = $reg->handle;
            $deps = $reg->deps;
            $ver = $reg->ver;
            $in_footer = $reg->args;
            wp_deregister_script($handle);
            wp_register_script($handle,$src,$deps,$ver,$in_footer);
        }
    }
}
add_action('wp_enqueue_scripts', 'replace_google_cdn', 99999);
add_action('admin_enqueue_scripts', 'replace_google_cdn', 99999);
