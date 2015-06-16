<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Plugin Name: Tietuku Avatar
 * Description: This WordPress plugin let you use Tietuku Avatar service to replace Gravatar
 * Version: 0.1.0
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
    $email = '';
    if ( is_numeric($id_or_email) ) {
        $id = (int) $id_or_email;
        $user = get_userdata($id);
        if ( $user )
            $email = $user->user_email;
    } elseif ( is_object($id_or_email) ) {
        // No avatar for pingbacks or trackbacks

        /**
         * Filter the list of allowed comment types for retrieving avatars.
         *
         * @since 3.0.0
         *
         * @param array $types An array of content types. Default only contains 'comment'.
         */
        $allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
        if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
            return false;

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_userdata($id);
            if ( $user )
                $email = $user->user_email;
        }

        if ( ! $email && ! empty( $id_or_email->comment_author_email ) )
            $email = $id_or_email->comment_author_email;
    } else {
        $email = $id_or_email;
    }

    $avatar = 'http://avatar.tietuku.com/avatar/'.md5(strtolower(trim($email))).'?size='.$size;
    $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

    return $avatar;
}