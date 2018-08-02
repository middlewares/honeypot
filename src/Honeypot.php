<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

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
     * Set the response factory used.
     */
    public function responseFactory(ResponseFactoryInterface $responseFactory): self
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isValid($request)) {
            $responseFactory = $this->responseFactory ?: Factory::getResponseFactory();

            return $responseFactory->createResponse(403);
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
