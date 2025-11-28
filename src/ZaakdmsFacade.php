<?php

namespace Woweb\Zaakdms;

use Illuminate\Support\Facades\Facade;

class ZaakdmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zaakdms';
    }
}