<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

namespace DefStudio\Html;

use ArrayAccess;
use DateTimeImmutable;
use DefStudio\Html\Elements\A;
use DefStudio\Html\Elements\Button;
use DefStudio\Html\Elements\Div;
use DefStudio\Html\Elements\Element;
use DefStudio\Html\Elements\Fieldset;
use DefStudio\Html\Elements\File;
use DefStudio\Html\Elements\Form;
use DefStudio\Html\Elements\I;
use DefStudio\Html\Elements\Img;
use DefStudio\Html\Elements\Input;
use DefStudio\Html\Elements\Label;
use DefStudio\Html\Elements\Legend;
use DefStudio\Html\Elements\Option;
use DefStudio\Html\Elements\Select;
use DefStudio\Html\Elements\Span;
use DefStudio\Html\Elements\Textarea;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;
use ReflectionException;

class Html
{
    use Macroable;

    const HTML_DATE_FORMAT = 'Y-m-d';
    const HTML_TIME_FORMAT = 'H:i:s';

    /** @var Request */
    protected $request;

    /** @var ArrayAccess|array */
    protected $model;

    private $autocomplete_enabled = true;

    private $name_patterns = [];

    private $draft_overrides = [];

    private $validation_rules = [];
    private $validation_on_labels_only = false;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function enable_autocomplete()
    {
        $this->autocomplete_enabled = true;
    }

    public function disable_autocomplete()
    {
        $this->autocomplete_enabled = false;
    }

    public function set_validation_rules(array $rules, bool $labels_only = false)
    {

        if (!empty($this->validation_rules)) return;

        $this->validation_on_labels_only = $labels_only;

        foreach ($rules as $rule_name => $rule) {

            $rule_name = str_replace(".", "\.", $rule_name);
            $rule_name = str_replace("*", "[\w]*", $rule_name);

            if (!is_array($rule)) $rule = explode("|", $rule);
            $rule = collect($rule);

            $this->validation_rules[$rule_name] = $rule;
        }
    }

    public function unset_validation_rules()
    {
        $this->validation_rules = [];
    }

    /**
     * Sets current name pattern to be substituted to $name argument
     *
     * @param $pattern
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function set_name_pattern($pattern)
    {
        $current_pattern = Arr::last($this->name_patterns);

        if (!empty($current_pattern)) {
            $pattern = $this->apply_name_pattern($pattern);
        }
        array_push($this->name_patterns, $pattern);

        return $this;
    }

    private function apply_name_pattern($name)
    {
        if(empty($name)){
            return $name;
        }

        if (Str::contains($name, '<..>')) {
            //apply_absolute_prefix
            $elements = explode('<..>', $name);
            $prefix = $elements[0];
            $name = $elements[1];
        }

        if (Str::startsWith($name, "/")) {
            //Don't use prefix
            return Str::substr($name, 1);
        }

        $pattern = Arr::last($this->name_patterns);

        if (!empty($prefix)) {
            if (empty($pattern)) {
                $pattern = "{$prefix}[::]";
            } else {
                $pieces = explode("[", $pattern);
                $pieces[0] = "{$prefix}[{$pieces[0]}]";
                $pattern = implode("[", $pieces);
            }

        }

        if (empty($name) || empty($pattern)) return $name;

        $extra = "";
        if (Str::contains($name, "[")) {
            $field = Str::before($name, "[");
            $extra = "[" . Str::after($name, "[");
            $name = $field;
        }
        return str_replace("::", $name, $pattern) . $extra;
    }

    /**
     * Deletes current name pattern setting
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function unset_name_pattern()
    {
        array_pop($this->name_patterns);
        return $this;
    }

    /**
     * @param BaseElement $input
     * @param string $helper_text
     *
     * @return BaseElement|Div
     * @throws Exceptions\InvalidHtml|ReflectionException
     * @noinspection PhpUnused
     */
    public function append_helper(BaseElement $input, string $helper_text)
    {
        return $this->append($input, $this->help($helper_text));
    }

    /**
     * Append an item to another one
     *
     * @param BaseElement $input
     * @param $item_to_append
     *
     * @return BaseElement|Div
     */
    public function append(BaseElement $input, $item_to_append)
    {
        // @formatter:off
        return $this->div()->class('input-group')
            ->addChild($input)
            ->addChild($this->div()
                ->class('input-group-append')
                ->addChild($this->span()
                    ->class('input-group-text')
                    ->addChild($item_to_append)
                ));
        // @formatter:on
    }

    /**
     * Displays an help symbol with given text
     *
     * @param $text
     * @param string $placement
     *
     * @return BaseElement|I
     * @throws Exceptions\InvalidHtml|ReflectionException
     */
    public function help($text, $placement = 'bottom')
    {
        return $this->icon("question")->popover($text, "Aiuto");
    }

    /**
     * Prints a FontAwesome icon
     *
     * @param string $name icon name
     * @param string $style icon style
     *
     * @return BaseElement|I
     * @throws Exceptions\InvalidHtml
     * @throws ReflectionException
     */
    public function icon($name, $style = "fas")
    {
        if (class_exists($name)) {
            $reflection_class = new ReflectionClass($name);
            $name = $reflection_class->getConstant('ICON') ?? '';
        }

        if (!Str::startsWith($name, "fa-")) $name = "fa-$name";
        return $this->i()->class($style)->class($name)->html(null);
    }

    /**
     * Displays an info symbol with given text
     * @param $text
     * @param string $placement
     * @return BaseElement|I
     * @throws Exceptions\InvalidHtml|ReflectionException
     */
    public function info($text, $placement = 'bottom')
    {
        return $this->icon("info")->tooltip($text, $placement);
    }

    /**
     * @param string|null $href
     * @param $contents
     * @return A
     */
    public function a($href = null, $contents = null)
    {
        return A::create()->attributeIf($href, 'href', $href)->html($contents);
    }

    /**
     * @param $contents
     *
     * @return I
     * @throws Exceptions\InvalidHtml
     */
    public function i($contents = null)
    {
        return I::create()->html($contents);
    }

    /**
     * @param $contents
     * @param string|null $type
     * @param null $name
     * @return Button
     */
    public function button($contents = null, $type = null, $name = null)
    {
        $name = $this->apply_name_pattern($name);

        //@formatter:off
        return Button::create()
            ->attributeIf($type, 'type', $type)
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->html($contents);
        //@formatter:on
    }

    public function collapse_button(string $target_id, $contents = null, $expanded = false)
    {
        //@formatter:off
        return $this->button($contents, 'button')
            ->id("$target_id-toggle-button")
            ->data('toggle', 'collapse')
            ->data('target', "#$target_id")
            ->attributes([
                'aria-expanded' => $expanded ? 'true' : 'false',
                'aria-controls' => $target_id,
            ]);
        //@formatter:on
    }

    /**
     * @param Collection|iterable|string $classes
     *
     * @return Htmlable
     */
    public function class($classes): Htmlable
    {
        if ($classes instanceof Collection) {
            $classes = $classes->toArray();
        }

        $attributes = new Attributes();
        $attributes->addClass($classes);

        return new HtmlString($attributes->render());
    }

    /**
     * @param string|null $name
     * @param bool $checked
     * @param string|null $value
     * @param bool $array_field
     * @return Input
     */
    public function checkbox($name = null, $checked = null, $value = '1')
    {
        $field_value = $this->old($name, $checked);

        if (Str::endsWith($name, "[]") && !($field_value instanceof Collection)) {


            if (empty($field_value)) $field_value = [];

            $field_values = Arr::wrap($field_value);

            //@formatter:off
            return $this->input('checkbox', $name, $value)
                ->id($this->dashed_field_name($name) . "_$value")
                ->attributeIf(!is_null($value), 'value', $value)
                ->attributeIf(in_array($value, $field_values), 'checked');
            //@formatter:on
        } else {
            //@formatter:off
            return $this->input('checkbox', $name, $value)
                ->attributeIf(!is_null($value), 'value', $value)
                ->attributeIf((bool)$this->old($name, $checked), 'checked');
            //@formatter:on
        }

    }

    public function custom_checkbox(string $name, string $label = '', $checked = null, $value = '1', $force_id = null, $inline = false)
    {
        $div = $this->div()->class('custom-control custom-checkbox hide-validation-message');

        if ($inline) {
            $div = $div->class('custom-control-inline');
        }

        $checkbox = $this->checkbox($name, $checked, $value)->class('custom-control-input')->attributeIf($force_id, 'id', $force_id);
        $label = $this->label($label)->for($checkbox->getAttribute('id'))->class('custom-control-label');

        $required_mark = $this->is_field_required($name) ? '<label class="field-required"></span>' : '';

        return $div->addChild($checkbox)->addChild($label)->addChild($required_mark);
    }

    public function custom_switch(string $name, string $label = '', $checked = null, $value = '1', $force_id = null)
    {
        $div = $this->div()->class('custom-control custom-switch');

        $radio = $this->checkbox($name, $checked, $value)->class('custom-control-input')->attributeIf($force_id, 'id', $force_id);
        $label = $this->label($label)->for($radio->getAttribute('id'))->class('custom-control-label');

        return $div->addChild($radio)->addChild($label);
    }


    /**
     * @param HtmlElement|string|null $contents
     *
     * @return Div
     */
    public function div($contents = null)
    {
        return Div::create()->children($contents);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return Input
     */
    public function email($name = null, $value = null)
    {
        return $this->input('email', $name, $value);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return Input
     */
    public function date($name = '', $value = null, $format = true)
    {
        $element = $this->input('date', $name, $value);

        if (!$format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime($element->getAttribute('value'), self::HTML_DATE_FORMAT));
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return Input
     */
    public function datetime($name = '', $value = null, $format = true)
    {
        $element = $this->input('datetime-local', $name, $value);

        if (!$format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime($element->getAttribute('value'), self::HTML_DATE_FORMAT . '\T' . self::HTML_TIME_FORMAT));
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param string|null $min
     * @param string|null $max
     * @param string|null $step
     *
     * @return Input
     */
    public function range($name = '', $value = '', $min = null, $max = null, $step = null)
    {
        return $this->input('range', $name, $value)->attributeIfNotNull($min, 'min', $min)->attributeIfNotNull($max, 'max', $max)->attributeIfNotNull($step, 'step', $step);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param bool $format
     *
     * @return Input
     */
    public function time($name = '', $value = null, $format = true)
    {
        $element = $this->input('time', $name, $value);

        if (!$format || empty($element->getAttribute('value'))) {
            return $element;
        }

        return $element->value($this->formatDateTime($element->getAttribute('value'), self::HTML_TIME_FORMAT));
    }

    /**
     * @param string $tag
     *
     * @return Element
     */
    public function element($tag)
    {
        return Element::withTag($tag);
    }

    /**
     * @param string|null $type
     * @param string|null $name
     * @param string|null $value
     *
     * @return Input
     */
    public function input($type = null, $name = null, $value = null)
    {

        $hasValue = $name && ($type !== 'password' && $type !== 'checkbox' && (!is_null($this->old($name, $value)) || !is_null($value)));

        //@formatter:off
        return Input::create()
            ->checks_error($this->dot_field_name($name))
            ->attributeIf($type, 'type', $type)
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->dashed_field_name($name))
            ->attributeIf(!$this->validation_on_labels_only && $this->is_field_required($name), 'required', 'true')
            ->attributeIf($hasValue, 'value', $this->old($name, $value));
        //@formatter:on

    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return Input
     */
    public function text($name = null, $value = null)
    {
        return $this->input('text', $name, $value)->disable_autocompleteIf(!$this->autocomplete_enabled);
    }

    /**
     * @param HtmlElement|string|null $legend
     *
     * @return Fieldset
     */
    public function fieldset($legend = null)
    {
        return $legend ? Fieldset::create()->legend($legend) : Fieldset::create();
    }

    /**
     * @param string $method
     * @param string|null $action
     *
     * @return Form
     */
    public function form($method = 'POST', $action = null)
    {
        $method = strtoupper($method);
        $form = Form::create();

        // If Laravel needs to spoof the form's method, we'll append a hidden
        // field containing the actual method
        if (in_array($method, [
            'DELETE',
            'PATCH',
            'PUT',
        ])) {
            $form = $form->addChild($this->hidden('_method')->value($method));
        }

        // On any other method that get, the form needs a CSRF token
        if ($method !== 'GET') {
            $form = $form->addChild($this->token());
        }

        return $form->method($method === 'GET' ? 'GET' : 'POST')->attributeIf($action, 'action', $action);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return Input
     */
    public function hidden($name = null, $value = null)
    {
        return $this->input('hidden', $name, $value);
    }

    /**
     * @param string|null $src
     * @param string|null $alt
     *
     * @return Img
     */
    public function img($src = null, $alt = null)
    {
        return Img::create()->attributeIf($src, 'src', $src)->attributeIf($alt, 'alt', $alt);
    }

    private function is_field_required($field_name): bool
    {
        if (empty($field_name)) return false;


        if (!empty($this->validation_rules)) {
            $field = $this->dot_field_name($field_name);

            foreach ($this->validation_rules as $rule_name => $rules) {
                if (preg_match("/$rule_name/", $field) && Str::contains($rule_name, $field_name)) {

                    if ($rules->contains('required')) return true;

                    if ($rules->contains(fn($value) => Str::startsWith($value, 'required_without'))) return true;

                    return false;
                }
            }

        }

        return false;
    }

    /**
     * @param HtmlElement|iterable|string|null $contents
     * @param string|null $for
     *
     * @return Label
     */
    public function label($contents = null, $for = null)
    {
        //@formatter:off
        return Label::create()
            ->classIf($this->is_field_required($for), 'field-required')
            ->attributeIf($for, 'for', $this->dashed_field_name($for))
            ->children($contents);
        //@formatter:on
    }

    /**
     * @param HtmlElement|string|null $contents
     *
     * @return Legend
     * @throws Exceptions\InvalidHtml
     */
    public function legend($contents = null)
    {
        return Legend::create()->html($contents);
    }

    /**
     * @param string $email
     * @param string|null $text
     *
     * @return A
     */
    public function mailto($email, $text = null)
    {
        return $this->a('mailto:' . $email, $text ?: $email);
    }

    /**
     * @param string|null $name
     * @param iterable|array $options
     * @param string|iterable|null $value
     *
     * @return Select
     */
    public function multiselect($name = null, $options = [], $value = null)
    {
        //@formatter:off
        return Select::create()
            ->checks_error($this->dot_field_name($name))
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->options($options)
            ->value($name ? $this->old($name, $value) : $value)
            ->multiple();
        //@formatter:on
    }

    /**
     * @param string $name
     * @param $value
     * @return Select
     * @noinspection PhpUnused
     */
    public function select_month($name = null, $value = null)
    {
        return $this->select($name, Lang::get('html::strings.months'), $value);
    }

    /**
     * @param string $name
     * @param $value
     * @return Select
     * @noinspection PhpUnused
     */
    public function multiselect_month($name = null, $value = null)
    {
        return $this->multiselect($name, Lang::get('html::strings.months'), $value);
    }

    public function float($name = null, $value = null, $min = null, $max = null, $step = null)
    {
        if (empty($step)) $step = "any";
        return $this->number($name, $value, $min, $max, $step);
    }

    /**
     * @param string|null $name
     * @param string|null $value
     * @param string|null $min
     * @param string|null $max
     * @param string|null $step
     *
     * @return Input
     */
    public function number($name = null, $value = null, $min = null, $max = null, $step = null)
    {
        return $this->input('number', $name, $value)->attributeIfNotNull($min, 'min', $min)->attributeIfNotNull($max, 'max', $max)->attributeIfNotNull($step, 'step', $step);
    }

    /**
     * @param string|null $text
     * @param string|null $value
     * @param bool $selected
     *
     * @return Option
     */
    public function option($text = null, $value = null, $selected = false)
    {
        return Option::create()->text($text)->value($value)->selectedIf($selected);
    }

    /**
     * @param string|null $name
     *
     * @return Input
     */
    public function password($name = null)
    {
        return $this->input('password', $name);
    }

    /**
     * @param string|null $name
     * @param bool $checked
     * @param string|null $value
     *
     * @return Input
     */
    public function radio($name = null, $checked = null, $value = null)
    {
        //@formatter:off
        return $this->input('radio', $name, $value)
            ->attributeIf($name, 'id', $value === null ? $this->dashed_field_name($name) : ($this->dashed_field_name($name) . '_' . $value))
            ->attributeIf(!is_null($value), 'value', $value)
            ->attributeIf((!is_null($value) && $this->old($name, null) == $value) || $checked, 'checked');
        //@formatter:on
    }


    public function custom_radio(string $name, string $label = '', $checked = null, $value = null)
    {
        $div = $this->div()->class('custom-control custom-radio hide-validation-message');

        $radio = $this->radio($name, $checked, $value)->class('custom-control-input');
        $label = $this->label($label)
            ->for($radio->getAttribute('id'))
            ->class('custom-control-label');

        $required_mark = $this->is_field_required($name)?'<label class="field-required"></span>':'';

        return $div->addChild($radio)->addChild($label)->addChild($required_mark);
    }


    /**
     * @param string|null $name
     * @param iterable|array $options
     * @param string|iterable|null $value
     *
     * @return Select
     */
    public function select($name = null, $options = [], $value = null)
    {
        //@formatter:off
        return Select::create()
            ->checks_error($this->dot_field_name($name))
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->dashed_field_name($name))
            ->options($options)
            ->value($name ? $this->old($name, $value) : $value);
        //@formatter:on
    }

    /**
     * @param HtmlElement|string|null $contents
     *
     * @return Span
     */
    public function span($contents = null)
    {
        return Span::create()->children($contents);
    }

    /**
     * @param string|null $text
     *
     * @return Button
     */
    public function submit($text = null)
    {
        return $this->button($text, 'submit');
    }

    /**
     * @param string|null $text
     *
     * @return Button
     */
    public function reset($text = null)
    {
        return $this->button($text, 'reset');
    }

    /**
     * @param string $number
     * @param string|null $text
     *
     * @return A
     */
    public function tel($number, $text = null)
    {
        return $this->a('tel:' . $number, $text ?: $number);
    }


    /**
     * @param string|null $name
     *
     * @return File
     */
    public function file($name = null)
    {
        //@formatter:off
        return File::create()
            ->checks_error($this->dot_field_name($name))
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name));
        //@formatter:on
    }

    /**
     * @param string|null $name
     * @param string|null $value
     *
     * @return Textarea
     */
    public function textarea($name = null, $value = null)
    {
        //@formatter:off
        return Textarea::create()
            ->checks_error($this->dot_field_name($name))
            ->attributeIf($name, 'name', $this->fieldName($name))
            ->attributeIf($name, 'id', $this->fieldName($name))
            ->value($this->old($name, $value));
        //@formatter:o0n
    }

    /**
     * @return Input
     */
    public function token()
    {
        return $this->hidden()->name('_token')->value(Session::token());
    }

    /**
     * @param ArrayAccess|array $model
     *
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param ArrayAccess|array $model
     * @param string|null $method
     * @param string|null $action
     *
     * @return Form
     */
    public function modelForm($model, $method = 'POST', $action = null): Form
    {
        $this->model($model);

        return $this->form($method, $action);
    }

    /**
     * @return $this
     */
    public function endModel()
    {
        $this->model = null;

        return $this;
    }

    /**
     * @return Htmlable
     */
    public function closeModelForm(): Htmlable
    {
        $this->endModel();

        return $this->form()->close();
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return mixed
     */
    protected function old($name, $value = null)
    {
        if (empty($name)) {
            return null;
        }

        $processed_name = $this->array_format_to_dot_notation($this->apply_name_pattern($name));


        // If there's no default value provided, the html builder currently
        // has a model assigned and there aren't old input items,
        // try to retrieve a value from the model.
        if (is_null($value) && $this->model && empty($this->request->old())) {
            $value = data_get($this->model, $processed_name) ?? '';
        }


        return $this->request->old($processed_name, $this->draft($name, $value));
    }

    public function has_draft()
    {
        return !empty($this->draft());
    }

    /**
     * Convert array format (sth[1]) to dot notation (sth.1)
     * @param $name
     * @noinspection RegExpRedundantEscape
     * @return string
     */
    private function array_format_to_dot_notation($name)
    {
        $name = preg_replace('/\[(.+)\]/U', '.$1', $name ?? '');
        $name = preg_replace('/\[\]/U', '', $name);
        return $name;
    }

    /**
     * Convert array format (sth[1]) to dash notation (sth_1)
     * @param $name
     * @return string
     */
    private function array_format_to_dash_notation($name)
    {
        return str_replace('.', '_', $this->array_format_to_dot_notation($name));
    }

    /**
     * Forces draft key to a new value
     * @param $name
     * @param $value
     * @return Html
     */
    public function set_draft_value($name, $value)
    {
        $name = $this->array_format_to_dot_notation($this->apply_name_pattern($name));
        data_set($this->draft_overrides, $name, $value);
        return $this;
    }

    public function draft($name = '', $default = null)
    {
        if (empty($name)) return Session::get('draft');

        $name = $this->array_format_to_dot_notation($this->apply_name_pattern($name));

        $value = Arr::get($this->draft_overrides, $name);

        if (empty($value)) {
            $value = Session::get("draft.$name", $default);
        }

        return $value;
    }


    /**
     * Retrieve the value from the current session or assigned model. This is
     * a public alias for `old`.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function value($name, $default = null)
    {
        $value = $this->old($name);
        if (empty($value)) return $default;
        return $value;
    }

    /**
     * Convert a field name to dot notation
     * @param $name
     * @return string
     */
    public function dot_field_name($name)
    {
        return $this->array_format_to_dot_notation($this->fieldName($name));
    }

    public function dashed_field_name($name)
    {
        return $this->array_format_to_dash_notation($this->fieldName($name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function fieldName($name)
    {
        return $this->apply_name_pattern($name);
    }

    /**
     * @throws Exception
     * @noinspection PhpUnused
     */
    protected function ensureModelIsAvailable()
    {
        if (empty($this->model)) {
            throw new Exception('Method requires a model to be set on the html builder');
        }
    }

    /**
     * @param string $value
     * @param string $format DateTime formatting string supported by date_format()
     * @return string
     */
    protected function formatDateTime($value, $format)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            $date = new DateTimeImmutable($value);

            return $date->format($format);
        } catch (Exception $e) {
            return $value;
        }
    }
}
