<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\Optgroup;
use DefStudio\Html\Test\TestCase;

class OptgroupTest extends TestCase
{
    /** @test */
    public function it_can_create_an_optgroup()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<optgroup></optgroup>',
            Optgroup::create()
        );
    }

    /** @test */
    public function it_can_create_an_element_with_a_label()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<optgroup label="Cats"></optgroup>',
            Optgroup::create()->label('Cats')
        );
    }
}
