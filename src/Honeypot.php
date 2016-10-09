<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Interop\Http\Middleware\DelegateInterface;

class Honeypot implements ServerMiddlewareInterface
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
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->isValid($request)) {
            return Utils\Factory::createResponse(403);
        }

        return $delegate->process($request);
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
