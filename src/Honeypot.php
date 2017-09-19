<?php

namespace Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
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
     *
     * @param string|null $name
     *
     * @return string
     */
    public static function getField($name = null)
    {
        $name = $name ?: self::$currentName;

        return sprintf('<input type="text" name="%s">', $name);
    }

    /**
     * Set the field name.
     *
     * @param string $name
     */
    public function __construct($name = 'hpt_name')
    {
        $this->name = self::$currentName = $name;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (!$this->isValid($request)) {
            return Utils\Factory::createResponse(403);
        }

        return $handler->handle($request);
    }

    /**
     * Check whether the request is valid.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function isValid(ServerRequestInterface $request)
    {
        $method = strtoupper($request->getMethod());

        if (in_array($method, ['GET', 'HEAD', 'CONNECT', 'TRACE', 'OPTIONS'], true)) {
            return true;
        }

        $data = $request->getParsedBody();

        return isset($data[$this->name]) && $data[$this->name] === '';
    }
}
