<?php
namespace App\HotRoll\Facades;

use Illuminate\Support\Facades\Facade;

class QmsDatabaseFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'qmsdb';
    }
}
