<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;

class Fieldset extends BaseElement
{
    protected $tag = 'fieldset';

    /**
     * @param \DefStudio\Html\HtmlElement|string $text
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
