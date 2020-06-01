<?php

namespace DefStudio\Html\Test\Html;

class ATest extends TestCase
{
    /** @test */
    public function it_can_create_an_a_element()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<a></a>',
            $this->html->a()
        );
    }

    /** @test */
    public function it_can_create_an_a_element_with_a_href()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<a href="https://spatie.be"></a>',
            $this->html->a('https://spatie.be')
        );
    }

    /** @test */
    public function it_can_create_an_a_element_with_a_href_and_text_contents()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<a href="https://spatie.be">DefStudio</a>',
            $this->html->a('https://spatie.be', 'DefStudio')
        );
    }

    /** @test */
    public function it_can_create_an_a_element_with_a_href_and_html_contents()
    {
        $this->assertHtmlStringEqualsHtmlString(
            '<a href="https://spatie.be/open-source">DefStudio <em>Open Source</em></a>',
            $this->html->a('https://spatie.be/open-source', 'DefStudio <em>Open Source</em>')
        );
    }
}
