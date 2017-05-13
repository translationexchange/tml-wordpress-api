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

/**
 * Prints debug to log
 *
 * @param $var
 */
function trex_debug($var) {
//    error_log(var_export($var, true));
}

/**
 * Convert post to JSON
 *
 * @param $post
 * @return array
 */
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
        'tags' => wp_get_post_tags($post->ID)
    );
}

/**
 * Renders an error message
 *
 * @param $msg
 * @return array
 */
function trex_api_render_error($msg)
{
    return array('status' => 'error', 'message' => $msg);
}


/**
 * Convert params to post
 *
 * @param $params
 * @return array
 */
function trex_prepare_post_params($params)
{
    return array(
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
}


/**
 * Create a pagination fragment
 *
 * @param $page
 * @param $per_page
 * @param $total_count
 * @return array
 */
function trex_pagination($page, $per_page, $total_count)
{
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


/**
 * Sanitize JSON response
 *
 * @param $data
 * @return mixed
 */
function trex_api_sanitize_response($data)
{
    return $data;
}

/**
 * Inserts or updates WPML post/page translation
 *
 * @param $data
 * @param $original_post_id
 * @param $post_type
 * @param $locale
 * @return array
 */
function trex_insert_or_update_wpml_translation($params, $original_post_id, $post_type, $locale)
{
    $data = trex_prepare_post_params($params);

    $lang = apply_filters('wpml_default_language', NULL);

    if ($locale == $lang) {
        return trex_api_render_error('Translation cannot have the same locale as the original post');
    }

    $translated_post_id = apply_filters('wpml_object_id', $original_post_id, $post_type, FALSE, $locale);

    if ($translated_post_id) {
        // simply update the translation of the page
        $data['ID'] = $translated_post_id;
        wp_insert_post($data);
        trex_update_extra_post_content($original_post_id, $translated_post_id, $params);

    } else {
        $translated_post_id = wp_insert_post($data);
        trex_update_post_metadata($original_post_id, $translated_post_id);
        trex_update_extra_post_content($original_post_id, $translated_post_id, $params);

        // https://wpml.org/wpml-hook/wpml_element_type/
        $wpml_element_type = apply_filters('wpml_element_type', $post_type);

        // get the language info of the original post
        // https://wpml.org/wpml-hook/wpml_element_language_details/
        $get_language_args = array('element_id' => $original_post_id, 'element_type' => $post_type);
        $original_post_language_info = apply_filters('wpml_element_language_details', null, $get_language_args);

        $set_language_args = array(
            'element_id' => $translated_post_id,
            'element_type' => $wpml_element_type,
            'trid' => $original_post_language_info->trid,
            'language_code' => $locale,
            'source_language_code' => $original_post_language_info->language_code
        );

        do_action('wpml_set_element_language_details', $set_language_args);
    }

    $post = get_post($translated_post_id);
    return trex_post_to_json($post);
}

/**
 * Inserts or updates Polylang post/page translation
 *
 * @param $data
 * @param $original_post_id
 * @param $locale
 * @return array
 */
function trex_insert_or_update_polylang_translation($params, $original_post_id, $locale)
{
    $data = trex_prepare_post_params($params);

    if ($locale == pll_default_language()) {
        return trex_api_render_error('Translation cannot have the same locale as the original post');
    }

    $translated_post_id = pll_get_post($original_post_id, $locale);

    if ($translated_post_id) {
        $data['ID'] = $translated_post_id;
        wp_insert_post($data);
        trex_update_extra_post_content($original_post_id, $translated_post_id, $params);

    } else {
        $translated_post_id = wp_insert_post($data);
        trex_update_post_metadata($original_post_id, $translated_post_id);
        trex_update_extra_post_content($original_post_id, $translated_post_id, $params);

        pll_set_post_language($translated_post_id, $locale);

        $language = pll_default_language();
        $mapping = array(
            $language => $original_post_id,
            $locale => $translated_post_id
        );

        $languages = pll_languages_list();
        foreach ($languages as $language) {
            $post_id = pll_get_post($original_post_id, $language);
            if ($post_id) $mapping[$language] = $post_id;
        }

        pll_save_post_translations($mapping);
    }

    $post = get_post($translated_post_id);
    return trex_post_to_json($post);
}

/**
 * Updates post metadata
 *
 * @param $original_post_id
 * @param $translated_post_id
 */
function trex_update_post_metadata($original_post_id, $translated_post_id) {
    $original_meta = get_post_meta($original_post_id);

    foreach($original_meta as $key => $value) {
        trex_debug($key);

        if (is_array($value))
            $value = $value[0];

        if (preg_match('/^a:\d+/', $value)) {
            $value = unserialize($value);
        }

        trex_debug($value);

        update_post_meta($translated_post_id, $key, $value);
    }
}

/**
 * Updates extra translated content
 *
 * @param $original_post_id
 * @param $translated_post_id
 * @param $data
 */
function trex_update_extra_post_content($original_post_id, $translated_post_id, $data)
{
    if (isset($data['extra']['themes']['Ichiban'])) {
        $ichiban = $data['extra']['themes']['Ichiban'];
        $original_meta = get_post_meta($original_post_id);

        if (isset($ichiban['splash'])) {
            $splash = unserialize($original_meta['splash'][0]);

            if (isset($ichiban['splash']['title'])) {
                $splash['title'] = $ichiban['splash']['title'];
            }

            if (isset($ichiban['splash']['subtitle'])) {
                $splash['subtitle'] = $ichiban['splash']['subtitle'];
            }

            if (isset($ichiban['splash']['content'])) {
                $splash['content'] = $ichiban['splash']['content'];
            }

            update_post_meta($translated_post_id, 'splash', $splash);
        }
    }
}

/**
 * Prepares additional content for translation
 *
 * @param $post_id
 * @param $data
 * @return mixed
 */
function trex_append_extra_post_content($post_id, $data)
{
    $theme = wp_get_theme();
    $data['extra'] = array();

    if ('Ichiban' == $theme->name) {
        $data['extra'] = array();

        $meta = get_post_meta($post_id);
        if (isset($meta['splash']) && is_array($meta) && count($meta['splash']) > 0) {
            $splash = unserialize($meta['splash'][0]);
            $splash_content = array();
            if (isset($splash['title']))
                $splash_content['title'] = $splash['title'];
            if (isset($splash['subtitle']))
                $splash_content['subtitle'] = $splash['subtitle'];
            if (isset($splash['content']))
                $splash_content['content'] = $splash['content'];
            $data['extra']['themes']['Ichiban']['splash'] = $splash_content;
        }

    }

    return $data;
}