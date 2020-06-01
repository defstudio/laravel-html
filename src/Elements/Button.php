<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Name;
use DefStudio\Html\Elements\Attributes\Type;
use DefStudio\Html\Elements\Attributes\Value;

class Button extends BaseElement
{
    use Value;
    use Name;
    use Type;

    protected $tag = 'button';

}
