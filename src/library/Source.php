<?php

namespace Carica\StatusMonitor\Library {

  interface Source {

    /**
     * @return \DOMDocument|NULL
     */
    public function read();

  }
}