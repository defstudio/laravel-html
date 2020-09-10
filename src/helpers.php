<?php

    use DefStudio\Html\Html;

    if(!function_exists('h')){
        /**
         * @return Html
         */
        function h(){
            return app(Html::class);
        }
    }

    if(!function_exists('Trans')){
        /**
         * @return \Illuminate\Contracts\Translation\Translator|string|array|null
         */
    	function Trans($key = null, array $replace = [], $locale = null){
    	    return ucfirst(trans($key, $replace, $locale));
        }
    }

    if(!function_exists('TRANS')){
        /**
         * @return \Illuminate\Contracts\Translation\Translator|string|array|null
         */
        function TRANS($key = null, array $replace = [], $locale = null){
            return strtoupper(trans($key, $replace, $locale));
        }
    }
