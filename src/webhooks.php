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
 * Adds webhooks to wordpress
 *
 * @param $params
 */
function trex_api_post_webhooks($params)
{
    if (!isset($params['webhooks'])) {
        add_option('trex_api_webhooks', $params['webhooks']);
    }
}

/**
 * Removes any webhooks from WordPress
 *
 * @param $params
 */
function trex_api_delete_webhooks($params)
{
    delete_option('trex_api_webhooks');
}

/**
 * Get webhook by key
 *
 * @param $key
 */
function trex_get_webhook($key)
{
    $webhooks = get_option('trex_api_webhooks');

    if (!$webhooks)
        return null;

    $webhooks = json_decode($webhooks, true);
    if (!isset($webhooks[$key]))
        return null;

    return $webhooks[$key];
}

/**
 * Trigger workflows
 *
 * @param $post_id
 * @return bool
 */
function trex_webhook_save_post($post_id)
{
    global $disable_webhooks;
    if ($disable_webhooks) return;

    try {
        $webhook = trex_get_webhook('save_post');
        if (!$webhook) return;

        $url = $webhook["url"];
//        $url = 'http://localhost:3000/v1/linked_accounts/tdhtqr8rq8e0404hi10joa1lqe9h0re14/callback';
        $post_type = get_post_type($post_id);
        wp_remote_post($url, array(
                'method' => 'POST',
                'timeout' => 10,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => false,
                'body' => array("post_id" => $post_id, "post_type" => $post_type))
        );

    } catch (Exception $e) {
//        echo 'Caught exception on triggering workflow: ', $e->getMessage(), "\n";
    }

    return true;
}

add_action('save_post', 'trex_webhook_save_post');
