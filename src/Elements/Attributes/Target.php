<?php

    namespace DefStudio\Html\Elements\Attributes;

    trait Target{
        /**
         * @param $target
         *
         * @return static
         */
        public function target($target){
            return $this->attribute('target', $target);
        }

        /**
         * @return static
         */
        public function target_blank(){
            return $this->attribute('target', '_blank');
        }
    }
