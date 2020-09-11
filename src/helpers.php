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

    if(!function_exists('uc_trans')){
        /**
         * @return \Illuminate\Contracts\Translation\Translator|string|array|null
         */
        function uc_trans($key = null, array $replace = [], $locale = null){
            return ucwords(trans($key, $replace, $locale));
        }
    }

    if(!function_exists('uf_trans')){
        /**
         * @return \Illuminate\Contracts\Translation\Translator|string|array|null
         */
        function uf_trans($key = null, array $replace = [], $locale = null){
            return ucfirst(trans($key, $replace, $locale));
        }
    }

    if(!function_exists('u_trans')){
        /**
         * @return \Illuminate\Contracts\Translation\Translator|string|array|null
         */
        function u_trans($key = null, array $replace = [], $locale = null){
            return strtoupper(trans($key, $replace, $locale));
        }
    }
