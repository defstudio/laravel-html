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
