<?php

namespace DefStudio\Html\Elements;

use DefStudio\Html\BaseElement;
use DefStudio\Html\Elements\Attributes\Autofocus;
use DefStudio\Html\Elements\Attributes\ChecksError;
use DefStudio\Html\Elements\Attributes\Disabled;
use DefStudio\Html\Elements\Attributes\Name;
use DefStudio\Html\Elements\Attributes\Readonly;
use DefStudio\Html\Elements\Attributes\Required;
use DefStudio\Html\Selectable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Select extends BaseElement{
    use Autofocus;
    use Disabled;
    use Name;
    use Required;
    use Readonly;
    use ChecksError;

    /** @var string */
    protected $tag = 'select';

    /** @var array */
    protected $options = [];

    /** @var string|iterable */
    protected $value = '';

    /**
     * @return static
     */
    public function multiple()
    {
        $element = clone $this;

        $element = $element->attribute('multiple');

        $name = $element->getAttribute('name');

        if($name && !Str::endsWith($name, '[]')){
            $element = $element->name($name . '[]');
        }

        $element = $element->class('multiselect');

        $element->applyValueToOptions();

        return $element;
    }

    /**
     * @param iterable $options
     *
     * @return static
     */
    public function options($options)
    {
        return $this->addChildren($options, function ($text, $value) {
            if (is_array($text)) {

                if(isset($text['label'])){
                    return Option::create()
                        ->value($value)
                        ->text($text['label'])
                        ->class(implode(' ', $text['classes']??[]))
                        ->dataIf(!empty($text['toggled-if']), 'toggled-if', $text['toggled-if']??'')
                        ->selectedIf($value === $this->value);
                }else{
                    return $this->optgroup($value, $text);
                }

            }

            return Option::create()
                ->value($value)
                ->text($text)
                ->selectedIf($value === $this->value);
        });
    }

    /**
     * @param string $label
     * @param iterable $options
     *
     * @return static
     */
    public function optgroup($label, $options)
    {
        return Optgroup::create()
            ->label($label)
            ->addChildren($options, function ($text, $value) {
                return Option::create()
                    ->value($value)
                    ->text($text)
                    ->selectedIf($value === $this->value);
            });

        return $this->addChild($optgroup);
    }

    /**
     * @param string|null $text
     *
     * @return static
     */
    public function placeholder($text)
    {
        return $this->prependChild(
            Option::create()
                ->value(null)
                ->text($text)
                ->selectedIf(! $this->hasSelection())
        );
    }

    /**
     * @param string|iterable $value
     *
     * @return static
     */
    public function value($value = null)
    {
        $element = clone $this;

        $element->value = $value;

        $element->applyValueToOptions();

        return $element;
    }

    public function fallback_value($value){
        if(empty($this->getAttribute('value'))){

            return $this->value($value);
        }
        return $this;
    }

    protected function hasSelection()
    {
        return $this->children->contains->hasAttribute('selected');
    }

    protected function applyValueToOptions()
    {
        $value = Collection::make($this->value);

        if (! $this->hasAttribute('multiple')) {
            $value = $value->take(1);
        }

        $this->children = $this->applyValueToElements($value, $this->children);
    }

    protected function applyValueToElements($value, Collection $children)
    {
        return $children->map(function ($child) use ($value) {
            if ($child instanceof Optgroup) {
                return $child->setChildren($this->applyValueToElements($value, $child->children));
            }

            if ($child instanceof Selectable) {
                return $child->selectedIf($value->contains($child->getAttribute('value')));
            }

            return $child;
        });
    }
}
