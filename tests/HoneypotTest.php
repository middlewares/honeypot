<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\Honeypot;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class HoneypotTest extends TestCase
{
    /**
     * @return array<int, list<array<string, int|string|null>|bool|string>>
     */
    public function honeypotProvider(): array
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
     *
     * @param array<string,string> $parsedBody
     */
    public function testHoneypot(string $method, array $parsedBody, bool $valid): void
    {
        $request = Factory::createServerRequest($method, '/')
            ->withParsedBody($parsedBody);

        $response = Dispatcher::run([
            new Honeypot(),
        ], $request);

        if ($valid) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    public function testHoneypotFields(): void
    {
        $this->assertEquals('<input type="text" name="hpt_name" aria-label="">', Honeypot::getField());
        $this->assertEquals(
            '<input type="text" name="foo" aria-label="Do not fill &quot;this&quot;">',
            Honeypot::getField('foo', 'Do not fill "this"')
        );

        $this->assertEquals('<input type="text" name="hpt_name" style="display: none">', Honeypot::getHiddenField());
        $this->assertEquals('<input type="text" name="foo" style="display: none">', Honeypot::getHiddenField('foo'));
    }
}
