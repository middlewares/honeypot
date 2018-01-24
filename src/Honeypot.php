<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Honeypot implements MiddlewareInterface
{
    /**
     * @var string
     */
    private static $currentName = 'hpt_name';

    /**
     * @var string
     */
    private $name = 'hpt_name';

    /**
     * Build a new honeypot field.
     */
    public static function getField(string $name = null): string
    {
        $name = $name ?: self::$currentName;

        return sprintf('<input type="text" name="%s">', $name);
    }

    /**
     * Set the field name.
     */
    public function __construct(string $name = 'hpt_name')
    {
        $this->name = self::$currentName = $name;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isValid($request)) {
            return Utils\Factory::createResponse(403);
        }

        return $handler->handle($request);
    }

    /**
     * Check whether the request is valid.
     */
    private function isValid(ServerRequestInterface $request): bool
    {
        $method = strtoupper($request->getMethod());

        if (in_array($method, ['GET', 'HEAD', 'CONNECT', 'TRACE', 'OPTIONS'], true)) {
            return true;
        }

        $data = $request->getParsedBody();

        return isset($data[$this->name]) && $data[$this->name] === '';
    }
}
