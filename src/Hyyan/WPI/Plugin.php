<?php

/**
 * This file is part of the hyyan/woo-poly-integration plugin.
 * (c) Hyyan Abo Fakher <hyyanaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hyyan\WPI;

use Hyyan\WPI\Tools\FlashMessages;

/**
 * Plugin
 *
 * @author Hyyan Abo Fakher <hyyanaf@gmail.com>
 */
class Plugin
{

    /**
     * Construct the plugin
     */
    public function __construct()
    {

        FlashMessages::register();

        add_action('init', array($this, 'activate'));
        add_action('plugins_loaded', array($this, 'loadTextDomain'));

        /* Registered anyway */
        new Emails();
    }

    /**
     * Load plugin langauge file
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain(
                'woo-poly-integration'
                , false
                , plugin_basename(dirname(Hyyan_WPI_DIR)) . '/languages'
        );
    }

    /**
     * Activate plugin
     *
     * The plugin will register its core if the requirements are full filled , otherwise
     * it will show an admin error message
     *
     * @return boolean false if plugin can not be activated
     */
    public function activate()
    {
        if (!static::canActivate()) {
            FlashMessages::remove(MessagesInterface::MSG_SUPPORT);
            FlashMessages::add(
                    MessagesInterface::MSG_ACTIVATE_ERROR
                    , static::getView('Messages/activateError')
                    , array('error')
                    , true
            );

            return false;
        }

        FlashMessages::remove(MessagesInterface::MSG_ACTIVATE_ERROR);
        FlashMessages::add(
                MessagesInterface::MSG_SUPPORT
                , static::getView('Messages/support')
        );

        $this->registerCore();
    }

    /**
     * Check if the plugin can be activated
     *
     * @return boolean true if can be activated , false otherwise
     */
    public static function canActivate()
    {
        $plugins = apply_filters('active_plugins', get_option('active_plugins'));
        $polylang = false;
        $woocommerce = false;

        // check polylang plugin 
        if (
                in_array('polylang/polylang.php', $plugins) ||
                in_array('polyland_pro/polylang.php', $plugins)
        ) {
            $polylang = true;
        }
        
        // check woocommerce plugin 
        if (in_array('woocommerce/woocommerce.php', $plugins)) {
            $woocommerce = true;
        }

        return $polylang && $woocommerce;
    }

    /**
     * Get current plugin version
     *
     * @return integer
     */
    public static function getVersion()
    {
        $data = get_plugin_data(Hyyan_WPI_DIR);

        return $data['Version'];
    }

    /**
     * Get plugin view
     *
     * @param string $name view name
     * @param array  $vars array of vars to pass to the view
     *
     * @return string the view content
     */
    public static function getView($name, array $vars = array())
    {
        $result = '';
        $path = dirname(Hyyan_WPI_DIR) . '/src/Hyyan/WPI/Views/' . $name . '.php';
        if (file_exists($path)) {
            ob_start();
            include($path);
            $result = ob_get_clean();
        }

        return $result;
    }

    /**
     * Add plugin core classes
     */
    protected function registerCore()
    {
        new Admin\Settings();
        new Cart();
        new Login();
        new Order();
        new Pages();
        new Endpoints();
        new Product\Product();
        new Taxonomies\Taxonomies();
        new Media();
        new Permalinks();
        new Language();
        new Coupon();
        new Reports();
        new Widgets\SearchWidget();
        new Widgets\LayeredNav();
        new Gateways();
    }

}
