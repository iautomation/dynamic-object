<?php

/**
* DynamicObject(originally ArrayAndObjectAccess from php.net)
* Array and Object access at the same time, with a change callback
*
* @authors Yousef Ismaeil <cliprz@gmail.com>, Joshua McKenzie <whereyoucanemailme@gmail.com>
*/

class DynamicObject implements ArrayAccess, Iterator {

	/**
	 * Data
	 *
	 * @var array
	 * @access private
	 */
	private $data = [];
    /**
     * onChange callback
     *
     * @var closure
     * @access private
     */
    private $change_callback = null;

    /**
     * Construct
     *
     * @param array data to initilize
     * @param closure onChange callback
     * @access public
     */
    public function __construct($data=[], $change_callback=null){
        $this->data = $data;
        $this->change_callback = $change_callback;
    }

	/**
	 * Get a data by key
	 *
	 * @param string The key data to retrieve
	 * @access public
	 */
	public function &__get ($key) {
		return $this->data[$key];
	}

	/**
	 * Assigns a value to the specified data
	 * 
	 * @param string The data key to assign the value to
	 * @param mixed  The value to set
	 * @access public 
	 */
	public function __set($key,$value) {
		$this->data[$key] = $value;
        if(!is_null($this->change_callback) && is_callable($this->change_callback)){
            call_user_func_array($this->change_callback, [$this, $key, $value]);
        }
	}

	/**
	 * Whether or not an data exists by key
	 *
	 * @param string An data key to check for
	 * @access public
	 * @return boolean
	 * @abstracting ArrayAccess
	 */
	public function __isset ($key) {
		return isset($this->data[$key]);
	}

	/**
	 * Unsets an data by key
	 *
	 * @param string The key to unset
	 * @access public
	 */
	public function __unset($key) {
		unset($this->data[$key]);
	}

	/**
	 * Assigns a value to the specified offset
	 *
	 * @param string The offset to assign the value to
	 * @param mixed  The value to set
	 * @access public
	 * @abstracting ArrayAccess
	 */
	public function offsetSet($offset,$value) {
		if (is_null($offset))$offset = count($this->data);
		$this->data[$offset] = $value;
        if(!is_null($this->change_callback) && is_callable($this->change_callback)){
            // $this->change_callback($this, $offset, $value);
            call_user_func_array($this->change_callback, [$this, $offset, $value]);
        }
	}

	/**
	 * Whether or not an offset exists
	 *
	 * @param string An offset to check for
	 * @access public
	 * @return boolean
	 * @abstracting ArrayAccess
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * Unsets an offset
	 *
	 * @param string The offset to unset
	 * @access public
	 * @abstracting ArrayAccess
	 */
	public function offsetUnset($offset) {
		if ($this->offsetExists($offset)) {
			unset($this->data[$offset]);
		}
	}

	/**
	 * Returns the value at specified offset
	 *
	 * @param string The offset to retrieve
	 * @access public
	 * @return mixed
	 * @abstracting ArrayAccess
	 */
	public function offsetGet($offset) {
		return $this->offsetExists($offset) ? $this->data[$offset] : null;
	}


	/**
	 * ArrayAccess methods
	 * 
	 * @param string The data key to assign the value to
	 * @param mixed  The value to set
	 * @access public 
	 */
	public function rewind() {
		return reset($this->data);
	}
	public function current() {
		return current($this->data);
	}
	public function key() {
		return key($this->data);
	}
	public function next() {
		return next($this->data);
	}
	public function valid() {
		return key($this->data) !== null;
	}

    /**
     * Sets change callback
     *
     * @param closure onChange callback
     * @access public
     */
    public function setChangeCallback($change_callback=null) {
        $this->change_callback = $change_callback;
    }

    /**
     * Converts data to array
     *
     * @access public
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * Converts data to object
     *
     * @access public
     */
    public function toObject() {
        return (object)$this->data;
    }

}
