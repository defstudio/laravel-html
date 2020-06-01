<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Value;
use DefStudio\Html\Selectable;

class Option extends BaseElement implements Selectable
{
    use Value;

    /** @var string */
    protected $tag = 'option';

    /**
     * @return static
     */
    public function selected()
    {
        return $this->attribute('selected', 'selected');
    }

    /**
     * @param bool $condition
     *
     * @return static
     */
    public function selectedIf($condition)
    {
        return $condition ?
            $this->selected() :
            $this->unselected();
    }

    /**
     * @return static
     */
    public function unselected()
    {
        return $this->forgetAttribute('selected');
    }
}
