<?php

    namespace DefStudio\Html;

    use Illuminate\Support\ServiceProvider;

    class HtmlServiceProvider extends ServiceProvider{
        public function register(){
            $this->app->singleton(Html::class);
        }

        public function boot(){
            $this->loadTranslationsFrom(__DIR__.'/Lang', 'html');
        }
    }
