<?php
declare(strict_types = 1);

namespace Middlewares;

use Middlewares\Utils\Traits\HasResponseFactory;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Honeypot implements MiddlewareInterface
{
    use HasResponseFactory;

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
     * Build a new honeypot field, hidden via inline CSS.
     */
    public static function getHiddenField(string $name = null): string
    {
        $name = $name ?: self::$currentName;

        return sprintf('<input type="text" name="%s" style="display: none">', $name);
    }

    /**
     * Set the field name.
     */
    public function __construct(string $name = 'hpt_name', ResponseFactoryInterface $responseFactory = null)
    {
        $this->name = self::$currentName = $name;
        $this->responseFactory = $responseFactory ?: Factory::getResponseFactory();
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isValid($request)) {
            return $this->createResponse(403);
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
