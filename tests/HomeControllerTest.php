<?php

declare(strict_types=1);

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends TestCase
{
    public function testIndexReturnsResponse(): void {
        $controller = new HomeController();
        $request = Request::create('/');
        $params = [];

        $response = $controller->index($request, $params);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Hello World!', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }
}

