<?php

namespace Basilicom\KoalityBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class KoalityBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/koality/js/pimcore/startup.js'
        ];
    }
}