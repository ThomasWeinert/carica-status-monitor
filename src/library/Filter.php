<?php

namespace Carica\StatusMonitor\Library {

  interface Filter {

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom);

  }
}