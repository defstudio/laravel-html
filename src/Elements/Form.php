<?php

    namespace DefStudio\Html\Elements;

    use DefStudio\Html\BaseElement;
    use DefStudio\Html\Elements\Attributes\Target;

    /**
     * Class Form
     *
     * @method disable_autocompleteIf($autocomplete_disabled)
     *
     * @package DefStudio\Html\Elements
     */
    class Form extends BaseElement{
        use Target;

        protected $tag = 'form';


        /**
         * @param string|null $action
         *
         * @return static
         */
        public function action($action){
            return $this->attribute('action', $action);
        }

        /**
         * @param string|null $method
         *
         * @return static
         */
        public function method($method){
            return $this->attribute('method', $method);
        }

        /**
         * @param bool $novalidate
         *
         * @return static
         */
        public function novalidate($novalidate = true){
            return $novalidate ? $this->attribute('novalidate') : $this->forgetAttribute('novalidate');
        }

        /**
         * @return static
         */
        public function acceptsFiles(){
            return $this->attribute('enctype', 'multipart/form-data');
        }

        public function disable_autocomplete(){
            h()->disable_autocomplete();
            return $this->attribute('autocomplete', 'off');
        }
    }
