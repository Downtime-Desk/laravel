<?php

namespace DowntimeDesk\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class DowntimeDesk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'downtime-desk';
    }
}
