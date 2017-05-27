<?php
/*
  Plugin Name: Translation Exchange API
  Plugin URI: http://wordpress.org/plugins/translationexchange-api/
  Description: Translation Exchange API for WordPress.
  Author: Translation Exchange, Inc
  Version: 0.1.4
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

require_once(dirname(__FILE__) . '/src/strategies/default_strategy.php');
require_once(dirname(__FILE__) . '/src/strategies/polylang_strategy.php');
require_once(dirname(__FILE__) . '/src/strategies/wpml_strategy.php');

global $trex_api_strategy;
global $disable_webhooks;

/**
 * Init Plugin
 */
function trex_api_init_plugin()
{
    global $trex_api_strategy;

    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
        $trex_api_strategy = new WpmlStrategy();
    } else if (is_plugin_active('polylang/polylang.php')) {
        $trex_api_strategy = new PolylangStrategy();
    } else {
        $trex_api_strategy = new DefaultStrategy();
    }
}

add_action('plugins_loaded', 'trex_api_init_plugin', 2);

include_once(dirname(__FILE__) . '/src/basic_auth.php');
include_once(dirname(__FILE__) . '/src/api.php');
include_once(dirname(__FILE__) . '/src/routes.php');
