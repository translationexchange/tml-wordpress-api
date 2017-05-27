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
 * Get localization strategy
 *
 * @param $params
 * @return mixed
 */
function trex_api_get_strategy($params)
{
    global $trex_api_strategy;
    return array('strategy' => $trex_api_strategy->getName());
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
    $langs = $trex_api_strategy->getLanguages($params);
    return array('languages' => $langs);
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
    $lang = $trex_api_strategy->getDefaultLanguage($params);
    return array('language' => $lang);
}

/**
 * Get posts
 *
 * @param $params
 * @return array
 */
function trex_api_get_posts($params)
{
    global $trex_api_strategy;
    return $trex_api_strategy->getPosts($params);
}

/**
 * Get a post
 *
 * @param $params
 * @return array
 */
function trex_api_get_post($params)
{
    global $trex_api_strategy;
    return $trex_api_strategy->getPost($params);
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

    global $disable_webhooks;
    $disable_webhooks = true;

    return $trex_api_strategy->insertOrUpdateTranslation($params, 'post');
}

/**
 * Get pages
 *
 * @param $params
 * @return array
 */
function trex_api_get_pages($params)
{
    global $trex_api_strategy;
    return $trex_api_strategy->getPages($params);
}

/**
 * Get a page
 *
 * @param $params
 * @return array
 */
function trex_api_get_page($params)
{
    global $trex_api_strategy;
    return $trex_api_strategy->getPage($params);
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

    global $disable_webhooks;
    $disable_webhooks = true;

    return $trex_api_strategy->insertOrUpdateTranslation($params, 'page');
}
