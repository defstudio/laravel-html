<?php

namespace DefStudio\Html\Test\Elements;

use DefStudio\Html\Elements\Element;
use DefStudio\Html\Exceptions\MissingTag;
use DefStudio\Html\Test\TestCase;

class ElementTest extends TestCase
{
    /** @test */
    public function it_can_create_an_element_with_a_tag()
    {
        $this->assertEquals(
            '<foo></foo>',
            Element::withTag('foo')
        );
    }

    /** @test */
    public function it_cant_create_an_element_without_a_tag()
    {
        $this->expectException(MissingTag::class);

        Element::create();
    }
}
