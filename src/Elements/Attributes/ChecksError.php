<?php
    /**
     * Copyright (c) 2020. Def Studio (assistenza@defstudio.it)
     */

    namespace DefStudio\Html\Elements\Attributes;

    use DefStudio\Html\Elements\Div;
    use DefStudio\Html\Exceptions\InvalidHtml;
    use Illuminate\Contracts\Support\Htmlable;
    use Illuminate\Support\HtmlString;
    use Illuminate\Support\Str;
    use Illuminate\Support\ViewErrorBag;
    use Session;

    trait ChecksError{
        private $has_error = false;
        private $messages = [];

        public function checks_error($name){
            /** @var ViewErrorBag $errors */
            $errors = Session::get('errors');
            if(!empty($errors)){
                if(!empty($name)){
                    if($errors->has($name)){
                        $this->has_error = true;
                        $this->messages = $errors->get($name);
                    }
                }
            }

            return $this;
        }

        public function clear_error(){
            $this->has_error = false;
        }

        /**
         * @return Htmlable|HtmlString
         * @throws InvalidHtml
         */
        public function render(){

            if($this->has_error){

                $this->has_error = false;

                $messages = "";
                foreach($this->messages as $message){
                    $messages .= "<li>$message</li>";
                }


                //@formatter:off
                $div = Div::create()
                          ->child($this->class('is-invalid'))
                          ->child(Div::create()
                          ->class('invalid-tooltip')
                          ->style('top: unset')
                          ->html("<ul>".$messages."</ul>")
                );
                //@formatter:on

                /** @var HtmlString $html */
                $html = $div->render()->toHtml();
                $html = Str::replaceFirst("<div>", "", $html);
                $html = Str::replaceLast("</div>", "", $html);
                return $html;
            }

            return parent::render();
        }

    }
