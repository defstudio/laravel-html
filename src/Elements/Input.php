<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Autofocus;
use DefStudio\Html\Elements\Attributes\ChecksError;
use DefStudio\Html\Elements\Attributes\CustomCheckbox;
use DefStudio\Html\Elements\Attributes\Disabled;
use DefStudio\Html\Elements\Attributes\MinMaxLength;
use DefStudio\Html\Elements\Attributes\Name;
use DefStudio\Html\Elements\Attributes\Placeholder;
use DefStudio\Html\Elements\Attributes\Readonly;
use DefStudio\Html\Elements\Attributes\Required;
use DefStudio\Html\Elements\Attributes\Type;
use DefStudio\Html\Elements\Attributes\Value;

class Input extends BaseElement
{
    use Autofocus;
    use Disabled;
    use MinMaxLength;
    use Name;
    use Placeholder;
    use Readonly;
    use Required;
    use Type;
    use Value;
    use ChecksError;

    protected $tag = 'input';

    protected $custom_checkbox_label;


    /**
     * @return static
     */
    public function unchecked(){
        return $this->checked(false);
    }

    /**
     * @param bool $checked
     *
     * @return static
     */
    public function checked($checked = true)
    {
        return $checked
            ? $this->attribute('checked', 'checked')
            : $this->forgetAttribute('checked');
    }

    /**
     * @param string|null $size
     *
     * @return static
     */
    public function size($size)
    {
        return $this->attribute('size', $size);
    }


}
