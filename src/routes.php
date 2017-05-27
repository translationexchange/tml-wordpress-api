<?php

/*
  Copyright (c) 2017 Translation Exchange, Inc. https://translationexchange.com

   _______                  _       _   _             ______          _
  |__   __|                | |     | | (_)           |  ____|        | |
     | |_ __ __ _ _ __  ___| | __ _| |_ _  ___  _ __ | |__  __  _____| |__   __ _ _ __   __ _  ___
     | | '__/ _` | '_ \/ __| |/ _` | __| |/ _ \| '_ \|  __| \ \/ / __| '_ \ / _` | '_ \ / _` |/ _ \
     | | | | (_| | | | \__ \ | (_| | |_| | (_) | | | | |____ >  < (__| | | | (_| | | | | (_| |  __/
     |_|_|  \__,_|_| |_|___/_|\__,_|\__|_|\___/|_| |_|______/_/\_\___|_| |_|\__,_|_| |_|\__, |\___|
                                                                                         __/ |
                                                                                        |___/
    GNU General Public License, version 2

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

    http://www.gnu.org/licenses/gpl-2.0.html
*/


add_action('rest_api_init', function () {
    register_rest_route('translationexchange/v1', '/strategy', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_strategy',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/webhooks', array(
        'methods' => 'POST',
        'callback' => 'trex_api_post_webhooks',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/webhooks', array(
        'methods' => 'DELETE',
        'callback' => 'trex_api_delete_webhooks',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/languages', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_languages',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/languages/default', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_default_language',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_posts',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_post',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/posts', array(
        'methods' => 'POST',
        'callback' => 'trex_api_post_posts',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        }
    ));

    register_rest_route('translationexchange/v1', '/pages', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_pages',
        'permission_callback' => function () {
            return current_user_can('edit_pages');
        }
    ));

    register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_page',
        'permission_callback' => function () {
            return current_user_can('edit_pages');
        }
    ));

    register_rest_route('translationexchange/v1', '/pages', array(
        'methods' => 'POST',
        'callback' => 'trex_api_post_pages',
        'permission_callback' => function () {
            return current_user_can('edit_pages');
        }
    ));

});