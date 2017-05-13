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

global $trex_api_strategy;

/**
 *
 */

function trex_api_init_plugin()
{
    global $trex_api_strategy;

    if (is_plugin_active('polylang/polylang.php')) {
        $trex_api_strategy = 'polylang';
    }
}

add_action('plugins_loaded', 'trex_api_init_plugin', 2);

function trex_api_sanitize_response($data)
{
    return $data;
}

function trex_api_get_strategy($data)
{
    global $trex_api_strategy;
    return trex_api_sanitize_response(array('strategy' => $trex_api_strategy));
}

function trex_api_get_languages($data)
{
    global $trex_api_strategy;
    $langs = array();

    if ($trex_api_strategy == 'polylang') {
        $langs = array('languages' => pll_languages_list(array()));
    }

    return $langs;
}

function trex_api_get_default_language($data)
{
    global $trex_api_strategy;
    $lang = 'en';

    if ($trex_api_strategy == 'polylang') {
        $lang = pll_default_language();
    }

    return array('language' => $lang);
}

function trex_api_get_current_language($data)
{
    global $trex_api_strategy;
    $lang = 'en';

    if ($trex_api_strategy == 'polylang') {
        $lang = pll_current_language();
    }

    return array('language' => $lang);
}

function trex_post_to_json($post)
{
    $content = $post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);

    $title = $post->post_title;
    $title = apply_filters('the_title', $title);
    $title = str_replace(']]>', ']]&gt;', $title);

    return array(
        'id' => $post->ID,
        'date' => $post->post_date,
        'date_gmt' => $post->post_date_gmt,
        'modified' => $post->post_modified,
        'modified_gmt' => $post->post_modified_gmt,
        'slug' => $post->post_name,
        'link' => get_permalink($post),
        'status' => $post->post_status,
        'type' => $post->post_type,
        'author' => $post->post_author,
        'featured_media' => $post->post_featured_media,
        'parent' => $post->post_parent,
        'template' => $post->post_template,
        'format' => get_post_format($post),
        'content' => array(
            'plain' => $post->post_content,
            'rendered' => $content
        ),
        'title' => array(
            'plain' => $post->post_title,
            'rendered' => $title
        ),
        'categories' => wp_get_post_categories($post->ID),
        'tags' => wp_get_post_tags($post->ID),
        'meta' => get_post_meta($post->ID),
    );
}

function trex_pagination($page, $per_page, $total_count) {
    $total_pages = round($total_count / $per_page);
    if ($total_count % $per_page > 0)
        $total_pages = $total_pages + 1;

    return array(
        'page' => $page,
        'per_page' => $per_page,
        'total_count' => $total_count,
        'total_pages' => $total_pages
    );
}

function trex_api_get_posts($data)
{
    $per_page = isset($data['per_page']) ? $data['per_page'] : 30;
    $page = isset($data['page']) ? $data['page'] : 1;
    $offset = ($page - 1) * $per_page;

    $query = array(
        'category' => isset($data['category']) ? $data['category'] : '',
        'category_name' => isset($data['category_name']) ? $data['category_name'] : '',
        'orderby' => isset($data['orderby']) ? $data['orderby'] : 'date',
        'order' => isset($data['order']) ? $data['order'] : 'DESC',
        'include' => isset($data['include']) ? $data['include'] : '',
        'exclude' => isset($data['exclude']) ? $data['exclude'] : '',
        'meta_key' => isset($data['meta_key']) ? $data['meta_key'] : '',
        'meta_value' => isset($data['meta_value']) ? $data['meta_value'] : '',
        'post_type' => isset($data['post_type']) ? $data['post_type'] : 'post',
        'post_mime_type' => isset($data['post_mime_type']) ? $data['post_mime_type'] : '',
        'post_parent' => isset($data['post_parent']) ? $data['post_parent'] : '',
        'author' => isset($data['author']) ? $data['author'] : '',
        'author_name' => isset($data['author_name']) ? $data['author_name'] : '',
        'post_status' => isset($data['post_status']) ? $data['post_status'] : 'publish',
        'suppress_filters' => isset($data['suppress_filters']) ? $data['suppress_filters'] : true
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

function trex_api_get_post($data)
{
    global $trex_api_strategy;

    if ($trex_api_strategy == 'polylang') {
        $locale = pll_default_language();
        $post = pll_get_post($data['id'], $locale);
        $post = get_post($post);
    } else {
        $post = get_post($data['id']);
    }

    return trex_post_to_json($post);
}

function trex_api_post_posts($data)
{
    global $trex_api_strategy;

    $post = wp_insert_post($data);

    if ($trex_api_strategy == 'polylang') {
        if (isset($data['locale']) && $data['locale'] != pll_default_language()) {
            pll_save_post_translations(array($data['locale'] => $post));
        }
    }

    $post = get_post($post);

    return trex_post_to_json($post);
}

function trex_api_get_pages($data)
{
    $per_page = isset($data['per_page']) ? $data['per_page'] : 30;
    $page = isset($data['page']) ? $data['page'] : 1;
    $offset = ($page - 1) * $per_page;

    $query = array(
        'sort_order' => isset($data['sort_order']) ? $data['sort_order'] : 'asc',
        'sort_column' => isset($data['sort_column']) ? $data['sort_column'] : 'post_title',
        'hierarchical' => isset($data['hierarchical']) ? $data['hierarchical'] : 1,
//        'exclude' => isset($data['exclude']) ? $data['exclude'] :'',
//        'include' => isset($data['include']) ? $data['include'] :'',
        'meta_key' => isset($data['meta_key']) ? $data['meta_key'] : '',
        'meta_value' => isset($data['meta_value']) ? $data['meta_value'] : '',
        'authors' => isset($data['authors']) ? $data['authors'] : '',
        'child_of' => isset($data['child_of']) ? $data['child_of'] : 0,
        'parent' => isset($data['parent']) ? $data['parent'] : -1,
        'exclude_tree' => isset($data['exclude_tree']) ? $data['exclude_tree'] : '',
        'number' => isset($data['number']) ? $data['number'] : '',
        'offset' => isset($data['offset']) ? $data['offset'] : 0,
        'post_type' => isset($data['post_type']) ? $data['post_type'] : 'page',
        'post_status' => isset($data['post_status']) ? $data['post_status'] : 'publish'
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

function trex_api_get_page($params)
{
    global $trex_api_strategy;

    if ($trex_api_strategy == 'polylang') {
        $locale = pll_default_language();
        $page = pll_get_post($params['id'], $locale);
        $page = get_page($page);
    } else {
        $page = get_page($params['id']);
    }

    return trex_post_to_json($page);
}

function trex_api_post_pages($params)
{
    global $trex_api_strategy;

    $data = array(
        'post_author' => isset($params['author']) ? $params['author'] : '',
        'post_date' => isset($params['date']) ? $params['date'] : '',
        'post_date_gmt' => isset($params['date_gmt']) ? $params['date_gmt'] : '',
        'post_content' => isset($params['content']) ? $params['content'] : '',
        'post_content_filtered' => isset($params['content_filtered']) ? $params['content_filtered'] : '',
        'post_title' => isset($params['title']) ? $params['title'] : '',
        'post_excerpt' => isset($params['excerpt']) ? $params['excerpt'] : '',
        'post_status' => isset($params['status']) ? $params['status'] : 'draft',
        'post_type' => isset($params['type']) ? $params['type'] : 'page',
        'comment_status' => isset($params['comment_status']) ? $params['comment_status'] : 'closed',
        'ping_status' => isset($params['ping_status']) ? $params['ping_status'] : 'closed',
        'post_password' => isset($params['password']) ? $params['password'] : '',
        'post_name' => isset($params['name']) ? $params['name'] : '',
        'to_ping' => isset($params['to_ping']) ? $params['to_ping'] : '',
        'pinged' => isset($params['pinged']) ? $params['pinged'] : '',
        'post_modified' => isset($params['modified']) ? $params['modified'] : '',
        'post_modified_gmt' => isset($params['modified_gmt']) ? $params['modified_gmt'] : '',
        'post_parent' => isset($params['parent']) ? $params['parent'] : 0,
        'menu_order' => isset($params['menu_order']) ? $params['menu_order'] : 0,
        'post_mime_type' => isset($params['mime_type']) ? $params['mime_type'] : 0,
        'guid' => isset($params['guid']) ? $params['guid'] : '',
        'post_category' => isset($params['category']) ? $params['category'] : '',
        'tax_input' => isset($params['tax_input']) ? $params['tax_input'] : array(),
        'meta_input' => isset($params['meta_input']) ? $params['meta_input'] : array(),
    );

    $page = wp_insert_post($data);

    if ($trex_api_strategy == 'polylang') {
        if (isset($params['locale']) && $params['locale'] != pll_default_language()) {
            pll_set_post_language($page, $params['locale']);

            $language = pll_default_language();
            $mapping = array(
                $language => $params['id'],
                $params['locale'] => $page
            );

            $languages = pll_languages_list();
            foreach($languages as $language) {
                $post_id = pll_get_post($params['id'], $language);
                if ($post_id) $mapping[$language] = $post_id;
            }

            pll_save_post_translations($mapping);
        }
    }

    return trex_api_get_page(array('id' => $page));
}

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

    register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'trex_api_put_post',
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

    register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'trex_api_put_page',
    ));

});