<?php
/**
* File based caching service.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Cache {

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
     * @var Library\FileSystem\Factory
     */
    private $_fileSystemFactory = NULL;

    /**
     *
     * @param string $bucket
     * @param Library\Cache\Configuration $configuration
     */
    public function __construct($bucket, Library\Cache\Configuration $configuration) {
      $this->validateName($bucket, 'Invalid cache element name.');
      $this->setDirectory($configuration->get('PATH', '').'/'.$bucket);
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory) {
      $this->_directory = $directory;
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
      $this->validateName($name, 'Invalid cache element name.');
      return $this->fileSystem->getFile(
        $this->_directory.'/'.$name.'/'.md5(serialize($parameters))
      );
    }

    /**
     * @param string $name
     * @return Libary\FileSystem\Directory
     */
    private function getDirectory($name) {
      $this->validateName($name, 'Invalid cache element name.');
      return $this->fileSystem->getDirectory(
         $this->_directory.'/'.$name
      );
    }

    /**
     * @param string $string
     * @param string $errorMessage
     * @throws UnexpectedValueException
     */
    private function validateName($string, $errorMessage) {
      if (!preg_match('(^[a-z\d]+)$', $string)) {
        throw new UnexpectedValueException($errorMessage);
      }
    }
  }
}