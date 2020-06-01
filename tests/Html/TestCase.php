<?php

namespace DefStudio\Html\Test\Html;

use DefStudio\Html\Html;
use DefStudio\Html\Test\Concerns\AssertsHtmlStrings;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;

abstract class TestCase extends \DefStudio\Html\Test\TestCase{
    use AssertsHtmlStrings;

    /** @var MockInterface */
    protected $request;

    /** @var array */
    protected $session = [];

    /** @var Html $html */
    protected $html;

    public function setUp(): void{
        parent::setUp();

        $this->request = Mockery::mock(Request::class);

        $this->request->shouldReceive('old')->withNoArgs()->andReturnUsing(function () {
                return $this->session;
            });

        $this->request->shouldReceive('old')->withAnyArgs()->andReturnUsing(function ($key, $value = null) {
                return $this->session[$key] ?? $value;
            });

        $session = Mockery::mock(Session::class)->shouldReceive('token')->andReturn('abc');

        $this->request->shouldReceive('session')->andReturn($session->getMock());

        $this->html = new Html($this->request);
    }

    protected function withModel(array $model){
        $this->html->model($model);

        return $this;
    }

    protected function withSession(array $session){
        $this->session = $session;

        return $this;
    }

    public function tearDown(): void{
        Mockery::close();
    }
}
