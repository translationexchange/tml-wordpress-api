<?php
/*
  Plugin Name: Translation Exchange API
  Plugin URI: http://wordpress.org/plugins/translationexchange-api/
  Description: Translation Exchange API for WordPress.
  Author: Translation Exchange, Inc
  Version: 0.1.2
  Author URI: https://translationexchange.com/
  License: GPLv2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 */

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

if (!defined('ABSPATH')) exit;

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

require_once(dirname(__FILE__) . '/src/helpers.php');

global $trex_api_strategy;

/**
 * Init Plugin
 */
function trex_api_init_plugin()
{
    global $trex_api_strategy;

    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
        $trex_api_strategy = 'wpml';
    } else if (is_plugin_active('polylang/polylang.php')) {
        $trex_api_strategy = 'polylang';
    }
}

add_action('plugins_loaded', 'trex_api_init_plugin', 2);

/**
 * Get localization strategy
 *
 * @param $params
 * @return mixed
 */
function trex_api_get_strategy($params)
{
    global $trex_api_strategy;
    return trex_api_sanitize_response(array('strategy' => $trex_api_strategy));
}

/**
 * Get available languages
 *
 * @param $params
 * @return mixed
 */
function trex_api_get_languages($params)
{
    global $trex_api_strategy;
    $langs = array();

    if ($trex_api_strategy == 'wpml') {
        $wpml_langs = apply_filters('wpml_active_languages', NULL, 'orderby=id&order=desc');
        foreach ($wpml_langs as $locale => $lang) {
            array_push($langs, $locale);
        }
    } else if ($trex_api_strategy == 'polylang') {
        $langs = pll_languages_list(array());
    }

    return trex_api_sanitize_response(array('languages' => $langs));
}

/**
 * Get default site language
 *
 * @param $params
 * @return mixed
 */
function trex_api_get_default_language($params)
{
    global $trex_api_strategy;
    $lang = 'en';

    if ($trex_api_strategy == 'wpml') {
        $lang = apply_filters('wpml_default_language', NULL);
    } else if ($trex_api_strategy == 'polylang') {
        $lang = pll_default_language();
    }

    return trex_api_sanitize_response(array('language' => $lang));
}

/**
 * Get current language
 *
 * @param $params
 * @return mixed
 */
function trex_api_get_current_language($params)
{
    global $trex_api_strategy;
    $lang = 'en';

    if ($trex_api_strategy == 'wpml') {
        $lang = apply_filters('wpml_current_language', NULL);
    } else if ($trex_api_strategy == 'polylang') {
        $lang = pll_current_language();
    }

    return trex_api_sanitize_response(array('language' => $lang));
}

/**
 * Get posts
 *
 * @param $params
 * @return array
 */
function trex_api_get_posts($params)
{
    $per_page = isset($params['per_page']) ? $params['per_page'] : 30;
    $page = isset($params['page']) ? $params['page'] : 1;
    $offset = ($page - 1) * $per_page;

    $query = array(
        'category' => isset($params['category']) ? $params['category'] : '',
        'category_name' => isset($params['category_name']) ? $params['category_name'] : '',
        'orderby' => isset($params['orderby']) ? $params['orderby'] : 'date',
        'order' => isset($params['order']) ? $params['order'] : 'DESC',
        'include' => isset($params['include']) ? $params['include'] : '',
        'exclude' => isset($params['exclude']) ? $params['exclude'] : '',
        'meta_key' => isset($params['meta_key']) ? $params['meta_key'] : '',
        'meta_value' => isset($params['meta_value']) ? $params['meta_value'] : '',
        'post_type' => isset($params['post_type']) ? $params['post_type'] : 'post',
        'post_mime_type' => isset($params['post_mime_type']) ? $params['post_mime_type'] : '',
        'post_parent' => isset($params['post_parent']) ? $params['post_parent'] : '',
        'author' => isset($params['author']) ? $params['author'] : '',
        'author_name' => isset($params['author_name']) ? $params['author_name'] : '',
        'post_status' => isset($params['post_status']) ? $params['post_status'] : 'publish',
        'suppress_filters' => isset($params['suppress_filters']) ? $params['suppress_filters'] : true
    );

    $posts = get_posts($query);
    $total_count = count($posts);

    $query['posts_per_page'] = $per_page;
    $query['offset'] = $offset;

    $posts = get_posts($query);

    $results = array();

    foreach ($posts as $post) {
        array_push($results, trex_post_to_json($post));
    }

    $pagination = trex_pagination($page, $per_page, $total_count);
    return array('results' => $results, 'pagination' => $pagination);
}

/**
 * Get a post
 *
 * @param $params
 * @return array
 */
function trex_api_get_post($params)
{
    $post = get_post($params['id']);
    return trex_post_to_json($post);
}

/**
 * Create or update post translation
 *
 * @param $params
 * @return array
 */
function trex_api_post_posts($params)
{
    global $trex_api_strategy;

    if (!isset($params['id'])) {
        return trex_api_render_error('Original post id must be provided');
    }

    if (!isset($params['locale'])) {
        return trex_api_render_error('Locale must be provided');
    }

    $data = trex_prepare_post_params($params);

    if ($trex_api_strategy == 'wpml') {
        return trex_insert_or_update_wpml_translation($data, $params['id'], 'post', $params['locale']);
    }

    if ($trex_api_strategy == 'polylang') {
        return trex_insert_or_update_polylang_translation($data, $params['id'], $params['locale']);
    }

    return trex_api_render_error('Unsupported strategy');
}

/**
 * Get pages
 *
 * @param $params
 * @return array
 */
function trex_api_get_pages($params)
{
    $per_page = isset($params['per_page']) ? $params['per_page'] : 30;
    $page = isset($params['page']) ? $params['page'] : 1;
    $offset = ($page - 1) * $per_page;

    $query = array(
        'sort_order' => isset($params['sort_order']) ? $params['sort_order'] : 'asc',
        'sort_column' => isset($params['sort_column']) ? $params['sort_column'] : 'post_title',
        'hierarchical' => isset($params['hierarchical']) ? $params['hierarchical'] : 1,
//        'exclude' => isset($params['exclude']) ? $params['exclude'] :'',
//        'include' => isset($params['include']) ? $params['include'] :'',
        'meta_key' => isset($params['meta_key']) ? $params['meta_key'] : '',
        'meta_value' => isset($params['meta_value']) ? $params['meta_value'] : '',
        'authors' => isset($params['authors']) ? $params['authors'] : '',
        'child_of' => isset($params['child_of']) ? $params['child_of'] : 0,
        'parent' => isset($params['parent']) ? $params['parent'] : -1,
        'exclude_tree' => isset($params['exclude_tree']) ? $params['exclude_tree'] : '',
        'number' => isset($params['number']) ? $params['number'] : '',
        'offset' => isset($params['offset']) ? $params['offset'] : 0,
        'post_type' => isset($params['post_type']) ? $params['post_type'] : 'page',
        'post_status' => isset($params['post_status']) ? $params['post_status'] : 'publish'
    );

    $pages = get_pages($query);
    $total_count = count($pages);

    $query['number'] = $per_page;
    $query['offset'] = $offset;

    $pages = get_pages($query);

    $results = array();
    foreach ($pages as $post) {
        array_push($results, trex_post_to_json($post));
    }

    $pagination = trex_pagination($page, $per_page, $total_count);

    return array('results' => $results, 'pagination' => $pagination);
}

/**
 * Get a page
 *
 * @param $params
 * @return array
 */
function trex_api_get_page($params)
{
    $page = get_page($params['id']);
    return trex_post_to_json($page);
}

/**
 * Create or update page translation
 *
 * @param $params
 * @return array
 */
function trex_api_post_pages($params)
{
    global $trex_api_strategy;

    if (!isset($params['id'])) {
        return trex_api_render_error('Original post id must be provided');
    }

    if (!isset($params['locale'])) {
        return trex_api_render_error('Locale must be provided');
    }

    $data = trex_prepare_post_params($params);

    if ($trex_api_strategy == 'wpml') {
        return trex_insert_or_update_wpml_translation($data, $params['id'], 'page', $params['locale']);
    }

    if ($trex_api_strategy == 'polylang') {
        return trex_insert_or_update_polylang_translation($data, $params['id'], $params['locale']);
    }

    return trex_api_render_error('Unsupported strategy');
}

/**
 * Setup routes
 */
add_action('rest_api_init', function () {
    register_rest_route('translationexchange/v1', '/strategy', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_strategy',
    ));

    register_rest_route('translationexchange/v1', '/languages', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_languages',
    ));

    register_rest_route('translationexchange/v1', '/languages/default', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_default_language',
    ));

    register_rest_route('translationexchange/v1', '/languages/current', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_current_language',
    ));

    register_rest_route('translationexchange/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_posts',
    ));

    register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_post',
    ));

    register_rest_route('translationexchange/v1', '/posts', array(
        'methods' => 'POST',
        'callback' => 'trex_api_post_posts',
    ));

    register_rest_route('translationexchange/v1', '/pages', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_pages',
    ));

    register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'trex_api_get_page',
    ));

    register_rest_route('translationexchange/v1', '/pages', array(
        'methods' => 'POST',
        'callback' => 'trex_api_post_pages',
    ));

});