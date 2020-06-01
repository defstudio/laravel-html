<?php

namespace DefStudio\Html;

use Illuminate\Contracts\Support\Htmlable;

interface HtmlElement{
    /**
     * @return Htmlable
     */
    public function render();
}
