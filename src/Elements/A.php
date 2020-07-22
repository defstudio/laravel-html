<?php

    namespace DefStudio\Html\Elements;

    use DefStudio\Html\BaseElement;

    class A extends BaseElement{
        protected $tag = 'a';

        /**
         * @param string|null $href
         *
         * @return static
         */
        public function href($href){
            return $this->attribute('href', $href);
        }

        public function target($target){
            return $this->attribute('target', $target);
        }
    }
