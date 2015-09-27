<?php

namespace DynamicObject;

/**
* DynamicObject(originally ArrayAndObjectAccess from php.net)
* Array and Object access at the same time, with a change callback
*
* @authors Yousef Ismaeil <cliprz@gmail.com>, Joshua McKenzie <whereyoucanemailme@gmail.com>
*/

class DynamicObject implements \ArrayAccess, \Iterator {

	/**
	 * Data
	 *
	 * @var array
	 * @access private
	 */
	private $data = [];
	/**
	 * Store changes keys
	 *
	 * @var array
	 * @access private
	 */
	private $changes = [];
	/**
	 * onChange callback
	 *
	 * @var closure
	 * @access private
	 */
	private $change_callback = null;
	/**
	 * save callback
	 *
	 * @var closure
	 * @access private
	 */
	private $save_callback = null;
	/**
	 * whether or not to store data/changes for closures
	 *
	 * @var boolean
	 * @access private
	 */
	public $store_closures = false;

	/**
	 * Construct
	 *
	 * @param array data to initilize
	 * @param closure onChange callback
	 * @access public
	 */
	public function __construct($data=[], $save_callback=null, $change_callback=null){
		if(is_array($data)){
			$this->data = $data;
		} else if(is_object($data)){
			$this->data = (array)$data;
		}
		$this->change_callback = $change_callback;
		$this->save_callback = $save_callback;
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
	public function __set($key, $value) {
		$this->data[$key] = $value;
		$this->changes[$key] = $value;
		$this->doChangeCallback($key, $value);
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
		$this->changes[$offset] = $value;
		$this->doChangeCallback($offset, $value);
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
	 * Catches any calls on data
	 *
	 * @param string method name
	 * @param string args
	 * @access public
	 * @return mixed
	 */
	public function __call($method, $args) {
		if(isset($this->data[$method]))
			return call_user_func_array($this->data[$method], $args);
	}

	/**
	 * Class called as function, will merge arguments into data and changes
	 *
	 * @param array arguments
	 * @access public
	 */
	public function __invoke($args=[]){
		$this->data = array_replace_recursive($this->data, $args);
		$this->changes = array_replace_recursive($this->changes, $args);
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
	 * Calls change callback
	 *
	 * @param string key
	 * @param string value
	 * @access private
	 */
	private function doChangeCallback($key, $value){
		if(!is_null($this->change_callback)){
			if(is_callable($this->change_callback))
				return call_user_func_array($this->change_callback, [$this, $key, $value]);
		}
	}

	/**
	 * Sets save callback
	 *
	 * @param closure save callback
	 * @access public
	 */
	public function setSaveCallback($save_callback=null) {
		$this->save_callback = $save_callback;
	}

	/**
	 * Calls save callback
	 *
	 * @param string key
	 * @param string value
	 * @access private
	 */
	private function doSaveCallback(){
		if(!is_null($this->save_callback)){
			if(is_callable($this->save_callback))
				return call_user_func_array($this->save_callback, [$this, $this->changes]);
		}
	}

	/**
	 * User save action
	 *
	 * @access private
	 */
	public function save(){
		return $this->doSaveCallback();
	}

	/**
	 * Converts data to array
	 *
	 * @access public
	 */
	public function dataArray() {
		return $this->data;
	}

	/**
	 * Converts data to object
	 *
	 * @access public
	 */
	public function dataObject() {
		return (object)$this->data;
	}

	/**
	 * Converts changes to array
	 *
	 * @access public
	 */
	public function changesArray() {
		return $this->changes;
	}

	/**
	 * Converts changes to object
	 *
	 * @access public
	 */
	public function changesObject() {
		return (object)$this->changes;
	}

}