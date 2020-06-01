<?php

namespace DefStudio\Html\Elements;

use Illuminate\Support\Collection;
use ReflectionClass;
use DefStudio\Html\Attributes;
use DefStudio\Html\BaseElement;

class Element extends BaseElement
{
    /**
     * @param string $tag
     *
     * @return static
     */
    public static function withTag($tag)
    {
        $element = (new ReflectionClass(static::class))->newInstanceWithoutConstructor();

        $element->tag = $tag;
        $element->attributes = new Attributes();
        $element->children = new Collection();

        return $element;
    }
}
