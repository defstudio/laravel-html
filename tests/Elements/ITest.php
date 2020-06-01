<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\I;
use DefStudio\Html\Test\TestCase;

class ITest extends TestCase
{
    /** @test */
    public function it_can_create_an_i_element()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<i></i>',
            I::create()
        );
    }
}
