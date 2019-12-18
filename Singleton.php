<?php
/***
 *                                       _           _____
 *                                      | |         |  ___|
 *      __ _  __ _  ___ _ __   ___ ___  | |__   ___ |___ \
 *     / _` |/ _` |/ _ \ '_ \ / __/ _ \ | '_ \ / _ \    \ \
 *    | (_| | (_| |  __/ | | | (_|  __/ | | | | (_) /\__/ /
 *     \__,_|\__, |\___|_| |_|\___\___| |_| |_|\___/\____/
 *            __/ |
 *           |___/
 *
 *           >> https://agenceho5.com
 */

namespace Nsi\Helpers;

abstract class Singleton
{
    private static $instance = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static final function getInstance(): Singleton
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    protected abstract function __construct();

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}