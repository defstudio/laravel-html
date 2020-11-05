<?php

namespace DefStudio\Html\Elements\Attributes;

trait Value
{
    /**
     * @param string|null $value
     *
     * @return static
     */
    public function value($value)
    {
        return $this->attribute('value', $value);
    }

    public function fallback_value($value){
        if(empty($this->getAttribute('value'))){
            $this->value($value);
        }
        return $this;
    }
}
