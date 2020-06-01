<?php

namespace DefStudio\Html;

interface HtmlElement
{
    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render();
}
