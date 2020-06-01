<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\Div;
use DefStudio\Html\Test\TestCase;

class DivTest extends TestCase
{
    /** @test */
    public function it_can_create_a_div()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<div></div>',
            Div::create()
        );
    }
}
