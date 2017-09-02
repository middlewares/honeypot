<?php

namespace Middlewares\Tests;

use Middlewares\Honeypot;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class HoneypotTest extends TestCase
{
    public function honeypotProvider()
    {
        return [
            ['POST', ['hpt_name' => 'not-null'], false],
            ['GET', ['hpt_name' => 'not-null'], true],
            ['POST', ['hpt_name' => ''], true],
            ['POST', ['hpt_name' => 0], false],
            ['POST', ['hpt_name' => null], false],
            ['POST', [], false],
            ['GET', [], true],
        ];
    }

    /**
     * @dataProvider honeypotProvider
     * @param mixed $method
     * @param mixed $valid
     */
    public function testHoneypot($method, array $parsedBody, $valid)
    {
        $request = Factory::createServerRequest([], $method)
            ->withParsedBody($parsedBody);

        $response = Dispatcher::run([
            new Honeypot(),
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);

        if ($valid) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    public function testHoneypotFields()
    {
        $this->assertEquals('<input type="text" name="hpt_name">', Honeypot::getField());
        $this->assertEquals('<input type="text" name="foo">', Honeypot::getField('foo'));
    }
}
