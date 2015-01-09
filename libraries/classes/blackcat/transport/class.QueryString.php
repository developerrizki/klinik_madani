<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:37 PM
 *
 * @package   transport
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * Class for query string manipulation
 * Edited by Lorenz for php 5 migration sdk
 *
 * @package     transport
 * @author      Erick Lazuardi  <erick@divkom.ee.itb.ac.id>
 * @author      Lorensius W. L. T <lorenz@londatiga.net>
 * @version     1.0
 * @copyright   Copyright (c) 2010 Lorensius W. L. T
 */
class QueryString
{
	/**
	 * Key and value pairs
     *
     * @var array
	 */
	private $_keyValuePairs = array();


	/**
	 * Constructor.
     * Create a new instance of this class
     *
	 * @param string $q Query string
	 *
	 * @return void
	 */
	public function __construct($q = null)
	{
        $q            = ($q == null) ? $_SERVER['QUERY_STRING'] : $q;
		$listKeyValue = explode('&', $q);

		if (is_array($listKeyValue) && count($listKeyValue)) {
			foreach ($listKeyValue as $item) {
				if (preg_match("/^\s*$/", $item)) continue;
				
				$temp = explode('=', $item);
				
				if ($temp[0] == 'url') continue;
				
				$this->_keyValuePairs[$temp[0]] = $temp[1];
			}
		}
	}

	/**
	 * Check if key / parameter name exists
     *
	 * @param string $key Key / parameter name
	 *
	 * @return bool TRUE on exists or FALSE otherwise
	 */
	public function exist($key)
	{
		return array_key_exists($key, $this->_keyValuePairs);
	}

	/**
	 * Get value of a key / parameter
     *
	 * @param string $key Key / parameter
	 *
	 * @return mixed  Value of a key
	 */
	public function getValue($key)
	{
		return (array_key_exists($key, $this->_keyValuePairs)) ? $this->_keyValuePairs[$key] : '';
	}


    /**
     * Update key and value
     *
     * @param string $key Key
     * @param mixed $value Key value
     *
     * @return void
     */
	public function update($key, $value)
	{
		$this->_keyValuePairs[$key] = $value;
	}


	/**
	 * Delete a key from key value pairs
     *
	 * @param string $key Key
	 *
	 * @return void
	 */
	public function delete($key)
	{
		if (array_key_exists($key, $this->_keyValuePairs)) {
			unset($this->_keyValuePairs[$key]);
		}
	}

	/**
	 * Add a key value pair
     *
	 * @param string $key Key
	 * @param string $value Key value
	 *
	 * @return void
	 */
	public function add($key, $value)
	{
		if (!array_key_exists($key, $this->_keyValuePairs)) {
		    $this->_keyValuePairs[$key] = $value;
		}
	}

	/**
	 * Convert to query string
	 *
	 * @return string Query string
	 */
	public function toString()
	{
		$temp = array();

		foreach ($this->_keyValuePairs as $key => $value) {
			$temp[] = "$key=$value";
		}

		return implode('&', $temp);
	}
}