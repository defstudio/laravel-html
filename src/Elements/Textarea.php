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
use DefStudio\Html\Exceptions\InvalidHtml;

class Textarea extends BaseElement{
    use Autofocus;
    use Placeholder;
    use Name;
    use Required;
    use Disabled;
    use IsReadonly;
    use MinMaxLength;
    use ChecksError;


    protected $tag = 'textarea';

    /**
     * @param string|null $value
     *
     * @return static
     * @throws InvalidHtml
     */
    public function value($value)
    {
        return $this->html($value);
    }

    /**
     * @param int $rows
     *
     * @return static
     */
    public function rows(int $rows)
    {
        return $this->attribute('rows', $rows);
    }

    /**
     * @param int $cols
     *
     * @return static
     */
    public function cols(int $cols)
    {
        return $this->attribute('cols', $cols);
    }
}
