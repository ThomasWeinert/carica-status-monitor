<?php
/**
* A factory object, to create file system objects for specified resources
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\FileSystem {

  /**
* A factory object, to create file system objects for specified resources
   */
  class Factory {

    /**
     * @param string $name
     * @return \Carica\StatusMonitor\Library\FileSystem\File
     */
    function getFile($name) {
      return new File($name);
    }

    /**
     * @param string $path
     * @return \Carica\StatusMonitor\Library\FileSystem\Directory
     */
    function getDirectory($path) {
      return new Directory($path);
    }

  }
}