<?php
/**
* Abstract class for configuration
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * If holds a list of configuration options that must be defined
   * in a child class. The value will always keep its first type.
   *
   */
  abstract class Configuration implements \ArrayAccess, \IteratorAggregate {

    /**
     * The definition list of options and default values
     *
     * @param string $options
     */
    protected $_options = array();

    /**
     * Check if the options exists
     *
     * @param string $name
     */
    public function offsetExists($name) {
      return array_key_exists($name, $this->_options);
    }

    /**
     * Read the option, the offset can be an array. If it ist the first
     * element is used as the name, the second as a default value.
     *
     * If the option value does not exists, or is NULL, the
     * default value will be returned.
     *
     * @param string|array $offset
     */
    public function offsetGet($offset) {
      if (is_array($offset)) {
        list($name, $defaultValue) = $offset;
      } else {
        $name = $offset;
        $defaultValue = NULL;
      }
      if (isset($this->_options[$name])) {
        return $this->_options[$name];
      } else {
        return $defaultValue;
      }
    }

    /**
     * Set a value, it will be converted into the type of the current value.
     *
     * @param string $name
     * @param scalar $value
     */
    public function offsetSet($name, $value) {
      if (!$this->offsetExists($name)) {
        throw new \InvalidArgumentException(
          sprintf('Invalid configuration option "%s".', $name)
        );
      }
      if ($this->_options[$name] !== NULL) {
        setType($value, getType($this->_options[$name]));
      }
      $this->_options[$name] = $value;
    }

    /**
     * Set the value to an empty one of the current type.
     *
     * @param string $name
     */
    public function offsetUnset($name) {
      if ($this->offsetExists($name)) {
        $type = getType($this->_options[$name]);
        $this->_options[$name] = NULL;
        setType($this->_options[$name], $type);
      }
    }

    /**
     * Provide list access to the options using the IteratorAggregate
     * interface.
     *
     * @return \ArrayIterator
     */
    public function getIterator() {
      return new \ArrayIterator($this->_options);
    }

    /**
     * Set a list of options from an array or a Traversable
     *
     * @param array|Traversable $options
     */
    public function assign($options) {
      if (is_array($options) || $options instanceOf Traversable) {
        foreach ($options as $name => $value) {
          try {
            $this[$name] = $value;
          } catch (\InvalidArgumentException $e) {
          }
        }
      }
    }
  }
}