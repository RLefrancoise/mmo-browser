<?php

namespace App;

/**
 * Class autoloading system
 *
 * @author	Mikael Randy <mrandy@prestaconcept.net>
 * @since	20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
 * @version 1.0 - 20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
 */
class Autoloader
{
    /**
     * Enable our specific autoloading system
     *
     * @author	Mikael Randy <mrandy@prestaconcept.net>
     * @since	20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
     * @version	1.0 - 20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
     */
    static public function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Autoloading system
     *
     * Transform namespace structure into directory structure (\NS1\NS2\NS3\className will be
     * search into __DIR__ . '/NS1/NS2/NS3/className.class.php').
     *
     * @author	Mikael Randy <mrandy@prestaconcept.net>
     * @since	20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
     * @version	1.0 - 20 juil. 2011 - Mikael Randy <mrandy@prestaconcept.net>
     * @param	string $class Class name (with entire namespace)
     */
    static public function autoload($class)
    {
        // Autoload only "sub-namespaced" class
        if (strpos($class, __NAMESPACE__.'\\') === 0)
        {
            // Delete current namespace from class one
            $relative_NS     = str_replace(__NAMESPACE__, '', $class);
            // Translate namespace structure into directory structure
            $translated_path = str_replace('\\', '/', $relative_NS);
            // Load class suffixed by ".class.php"
            require __DIR__ . '/' . $translated_path . '.php';
        }
    }
}
