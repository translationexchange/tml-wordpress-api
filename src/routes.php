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

if (has_action('rest_api_init')) {

    add_action('rest_api_init', function () {
        register_rest_route('translationexchange/v1', '/strategy', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return array('strategy' => $trex_api_strategy->getName());
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/webhooks', array(
            'methods' => 'POST',
            'callback' => function ($params) {
                try {
                    if (!isset($params['webhooks'])) {
                        add_option('trex_api_webhooks', $params['webhooks']);
                    }
                    return array("status" => "Ok");
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/webhooks', array(
            'methods' => 'DELETE',
            'callback' => function ($params) {
                try {
                    delete_option('trex_api_webhooks');
                    return array("status" => "Ok");
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/languages', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getLanguages($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/languages', array(
            'methods' => 'POST',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->addLanguages($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/languages/default', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getDefaultLanguage($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/posts', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPosts($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPost($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)/translations', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPostTranslations($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/posts/(?P<id>\d+)/translations', array(
            'methods' => 'POST',
            'callback' => function ($params) {
                try {
                    global $disable_webhooks;
                    $disable_webhooks = true;
                    global $trex_api_strategy;
                    return $trex_api_strategy->insertOrUpdateTranslation($params, 'post');
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/pages', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPages($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_pages');
            }
        ));

        register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPage($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_pages');
            }
        ));

        register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)/translations', array(
            'methods' => 'GET',
            'callback' => function ($params) {
                try {
                    global $trex_api_strategy;
                    return $trex_api_strategy->getPageTranslations($params);
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ));

        register_rest_route('translationexchange/v1', '/pages/(?P<id>\d+)/translations', array(
            'methods' => 'POST',
            'callback' => function ($params) {
                try {
                    global $disable_webhooks;
                    $disable_webhooks = true;
                    global $trex_api_strategy;
                    return $trex_api_strategy->insertOrUpdateTranslation($params, 'page');
                } catch (Exception $ex) {
                    return array('status' => 'error', 'message' => $ex->getMessage());
                }
            },
            'permission_callback' => function () {
                return current_user_can('edit_pages');
            }
        ));

    });
}
