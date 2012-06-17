<?php

namespace Carica\StatusMonitor\Library {

  interface Filter {

    public function filter(\DOMDocument $dom);

  }
}