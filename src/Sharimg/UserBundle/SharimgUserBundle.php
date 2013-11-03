<?php

namespace Sharimg\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SharimgUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
