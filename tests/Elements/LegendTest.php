<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\Legend;
use DefStudio\Html\Test\TestCase;

class LegendTest extends TestCase
{
    /** @test */
    public function it_can_create_a_legend()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<legend></legend>',
            Legend::create()
        );
    }
}
