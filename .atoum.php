<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';

use
    mageekguy\atoum\xml
;

$runner
    ->addExtension(new xml\extension($script))
    ->addExtension(new \Atoum\PraspelExtension\Manifest())
;

$script->noCodeCoverageForNamespaces('mageekguy\atoum\asserters');
$script->noCodeCoverageForClasses('mageekguy\atoum\asserter');
