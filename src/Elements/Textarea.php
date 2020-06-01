<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Autofocus;
use DefStudio\Html\Elements\Attributes\Disabled;
use DefStudio\Html\Elements\Attributes\MinMaxLength;
use DefStudio\Html\Elements\Attributes\Name;
use DefStudio\Html\Elements\Attributes\Placeholder;
use DefStudio\Html\Elements\Attributes\Readonly;
use DefStudio\Html\Elements\Attributes\Required;

class Textarea extends BaseElement
{
    use Autofocus;
    use Placeholder;
    use Name;
    use Required;
    use Disabled;
    use Readonly;
    use MinMaxLength;

    protected $tag = 'textarea';

    /**
     * @param string|null $value
     *
     * @return static
     * @throws \DefStudio\Html\Exceptions\InvalidHtml
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
