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

    public function setDirectory($directory) {
      $this->_directory = $directory;
    }

    /**
     * Read a cache element if it is not older then the given expire.
     *
     * @param string $name
     * @param mixed $parameters
     * @param integer $expires Seconds
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
     * @param intger $expires
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

    public function fileSystem(Library\FileSystem\Factory $factory) {
      if (isset($factory)) {
        $this->_fileSystemFactory = $factory;
      } elseif (NULL == $this->_fileSystemFactory) {
        $this->_fileSystemFactory = new Library\FileSystem\Factory();
      }
      return $this->_fileSystemFactory;
    }

    private function getFile($name, $parameters) {
      $this->validateName($name, 'Invalid cache element name.');
      return $this->fileSystem->getFile(
        $this->_directory.'/'.$name.'/'.md5(serialize($parameters))
      );
    }

    private function getDirectory($name) {
      $this->validateName($name, 'Invalid cache element name.');
      return $this->fileSystem->getDirectory(
         $this->_directory.'/'.$name
      );
    }

    private function validateName($string, $errorMessage) {
      if (!preg_match('(^[a-z\d]+)$', $string)) {
        throw new UnexpectedValueException($errorMessage);
      }
    }
  }
}