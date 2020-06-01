<?php

namespace DefStudio\Html\Facades;

use Illuminate\Support\Facades\Facade;
use DefStudio\Html\Html as HtmlBuilder;

class Html extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \DefStudio\Html\Html
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return HtmlBuilder::class;
    }
}
