<?php
/**
* File based caching service.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Cache\Service {

  use Carica\StatusMonitor\Library as Library;

  /**
   * File based caching service
   */
  class File implements Library\Cache\Service {

    /**
     * @var string
     */
    private $_directory = '';


    /**
     * @var string
     */
    private $_bucket = '';

    /**
     * @var Library\FileSystem\Factory
     */
    private $_fileSystemFactory = NULL;

    /**
     *
     * @param string $bucket
     * @param Library\Cache\Configuration $configuration
     */
    public function __construct($bucket, Library\Cache\Configuration $configuration) {
      $this->validateName($bucket, 'Invalid cache bucket name: '.$bucket);
      $this->_directory = $configuration[['PATH', '']];
      $this->_bucket = $bucket;
    }

    public function usable() {
      $path = $this->fileSystem()->getDirectory($this->_directory);
      if (!$path->exists()) {
        throw new LogicException(
          sprintf('Cache directory "%s" not found.', $this->_directory)
        );
      }
      if (!$path->isReadable()) {
        throw new LogicException(
          sprintf('Cache directory "%s" not readable.', $this->_directory)
        );
      }
      if (!$path->isWriteable()) {
        throw new LogicException(
          sprintf('Cache directory "%s" not writeable.', $this->_directory)
        );
      }
    }

    /**
     * Read a cache element if it is not older then the given expire.
     *
     * @param string $name
     * @param mixed $parameters
     * @param integer $expires Seconds
     * @return NULL
     */
    public function read($name, $parameters, $expires = 0) {
      $file = $this->getFile($name, $parameters);
      $now = time();
      if ($file->isReadable() && $file->modified() > $now - $expires) {
        return unserialize($file->read());
      }
      return NULL;
    }

    /**
     * Write data to cache
     *
     * @param string $name
     * @param mixed $parameters
     * @param integer $expires
     * @param mixed $data
     */
    public function write($name, $parameters, $expires, $data) {
      $this->usable();
      $this->getDirectory($name)->force();
      $file = $this->getFile($name, $parameters);
      $file->write(serialize($data));
    }

    /**
     * Remove/clear a part or the whole cache
     *
     * @param string $bucket
     * @param string $name
     * @param mixed $parameters
     */
    public function invalidate($name = NULL, $parameters = NULL) {
      $this->getFile($name, $parameters)->delete();
    }

    /**
     * @param NULL|Library\FileSystem\Factory $factory
     * @return Library\FileSystem\Factory
     */
    public function fileSystem(Library\FileSystem\Factory $factory = NULL) {
      if (isset($factory)) {
        $this->_fileSystemFactory = $factory;
      } elseif (NULL == $this->_fileSystemFactory) {
        $this->_fileSystemFactory = new Library\FileSystem\Factory();
      }
      return $this->_fileSystemFactory;
    }

    /**
     * @param string $name
     * @param mixed $parameters
     * @return Libary\FileSystem\File
     */
    private function getFile($name, $parameters) {
      $this->validateName($name, 'Invalid cache element name: '.$name);
      return $this->fileSystem()->getFile(
        implode(
          DIRECTORY_SEPARATOR,
          [
            $this->_directory,
            $this->_bucket,
            $name,
            md5(serialize($parameters))
          ]
        )
      );
    }

    /**
     * @param string $name
     * @return Libary\FileSystem\Directory
     */
    private function getDirectory($name) {
      $this->validateName($name, 'Invalid cache element name: '.$name);
      return $this->fileSystem()->getDirectory(
        implode(
          DIRECTORY_SEPARATOR,
          [
            $this->_directory,
            $this->_bucket,
            $name
          ]
        )
      );
    }

    /**
     * @param string $string
     * @param string $errorMessage
     * @throws UnexpectedValueException
     */
    private function validateName($string, $errorMessage) {
      if (!preg_match('(^[A-Za-z\d_-]+$)D', $string)) {
        throw new \UnexpectedValueException($errorMessage);
      }
    }
  }
}