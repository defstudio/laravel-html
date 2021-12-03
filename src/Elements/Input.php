<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Autofocus;
use DefStudio\Html\Elements\Attributes\ChecksError;
use DefStudio\Html\Elements\Attributes\Disabled;
use DefStudio\Html\Elements\Attributes\MinMaxLength;
use DefStudio\Html\Elements\Attributes\Name;
use DefStudio\Html\Elements\Attributes\Placeholder;
use DefStudio\Html\Elements\Attributes\IsReadonly;
use DefStudio\Html\Elements\Attributes\Required;
use DefStudio\Html\Elements\Attributes\Type;
use DefStudio\Html\Elements\Attributes\Value;

/**
 * Class Input
 *
 * @method readonlyIf($locked)
 * @method disable_autocompleteIf($autocomplete_disabled)
 * @package DefStudio\Html\Elements
 */
class Input extends BaseElement
{
    use Autofocus;
    use Disabled;
    use MinMaxLength;
    use Name;
    use Placeholder;
    use IsReadonly;
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
    public function size($size){
        return $this->attribute('size', $size);
    }

    public function disable_autocomplete(){
        return $this->attribute('autocomplete', 'autocomplete_disabled');
    }

}
