<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\HtmlElement;

class Fieldset extends BaseElement
{
    protected $tag = 'fieldset';

    /**
     * @param HtmlElement|string $text
     *
     * @return static
     */
    public function legend($contents)
    {
        return $this->prependChild(
            Legend::create()->text($contents)
        );
    }
}
