<?php
    /**
     * Copyright (c) 2020. Def Studio (assistenza@defstudio.it)
     */

    namespace DefStudio\Html\Elements\Attributes;

    use Illuminate\Support\ViewErrorBag;
    use Session;

    trait ChecksError{

        public function checks_error($name){
            /** @var ViewErrorBag $errors */
            $errors = Session::get('errors');
            if(!empty($errors)){
                if(!empty($name)){
                    if($errors->has($name)){
                       return $this->class('is-invalid');
                    }
                }
            }

            return $this;
        }

    }
