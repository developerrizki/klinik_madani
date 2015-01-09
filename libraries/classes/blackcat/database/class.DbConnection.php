<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 08:01 AM
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   2.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Loader class
 */
require_once CLASS_DIR . '/blackcat/system/class.Loader.php';

/**
 * IOException class
 */
require_once CLASS_DIR . '/blackcat/io/class.IOException.php';

/**
 * DbException class
 */
require_once CLASS_DIR . '/blackcat/database/class.DbException.php';


/**
 * Database connection object factory
 *
 * Currently supported database:
 * - Mysql
 * - PostgreSQL
 *
 * @package   database
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   2.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class DbConnection
{

    /**
     * Get instance of a specific database driver
     *
     * @param string $driver Database driver name
     *
     * @throws DbException If database driver does not exist
     *
     * @return object Instance of database driver, null if driver does not exist
     */
    static public function getInstance($driver)
    {
        try {
            Loader::loadClass($driver, CLASS_DIR . '/blackcat/database/drivers');

            return new $driver;
        } catch (IOException $e) {
            throw new DbException("Can not load database driver <i>$driver</i><br />" . $e->getMessage());
        }

        return null;
    }
}