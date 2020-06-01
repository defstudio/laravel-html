<?php

use DefStudio\Html\Html;

if (! function_exists('html')) {
    /**
     * @return \DefStudio\Html\Html
     */
    function html()
    {
        return app(Html::class);
    }
}
