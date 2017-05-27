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

class WpmlStrategy extends DefaultStrategy
{
    public function getName()
    {
        return 'wpml';
    }

    public function getLanguages($params)
    {
        $langs = array();

        $wpml_langs = apply_filters('wpml_active_languages', NULL, 'orderby=id&order=desc');
        foreach ($wpml_langs as $locale => $lang) {
            array_push($langs, $locale);
        }

        return $langs;
    }

    public function getDefaultLanguage($params)
    {
        return apply_filters('wpml_default_language', NULL);
    }


    public function insertOrUpdateTranslation($params, $post_type)
    {
        if (!isset($params['id'])) {
            return $this->renderApiError('Original post id must be provided');
        }

        $original_post_id = $params['id'];

        if (!isset($params['locale'])) {
            return $this->renderApiError('Locale must be provided');
        }

        $locale = $params['locale'];

        $data = $this->preparePostParams($params);

        $lang = apply_filters('wpml_default_language', NULL);

        if ($locale == $lang) {
            return $this->renderApiError('Translation cannot have the same locale as the original post');
        }

        $translated_post_id = apply_filters('wpml_object_id', $original_post_id, $post_type, FALSE, $locale);

        if ($translated_post_id) {
            // simply update the translation of the page
            $data['ID'] = $translated_post_id;
            wp_insert_post($data);
            $this->updateExtraPostContent($original_post_id, $translated_post_id, $params);

        } else {
            $translated_post_id = wp_insert_post($data);
            $this->updatePostMetadata($original_post_id, $translated_post_id);
            $this->updateExtraPostContent($original_post_id, $translated_post_id, $params);

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
        return $this->postToJson($post);
    }
}