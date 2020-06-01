<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\Fieldset;
use DefStudio\Html\Test\TestCase;

class FieldsetTest extends TestCase
{
    /** @test */
    public function it_can_create_a_fieldset()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<fieldset></fieldset>',
            Fieldset::create()
        );
    }

    /** @test */
    public function it_can_add_a_legend_to_the_fieldset()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<fieldset><legend>Legend</legend></fieldset>',
            Fieldset::create()->legend('Legend')
        );
    }
}
