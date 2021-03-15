<?php
declare(strict_types=1);

namespace User\Handler;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AbstractActionHandler
 * @package User\Handler
 */
abstract class AbstractActionHandler implements MiddlewareInterface
{
    protected string $redirectBase = '/';

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $action = $this->transformPhraseToCamelCase($request->getAttribute('action')) . 'Action';
        if (! method_exists($this, $action)) {
            // send 404
            return $handler->handle($request);
        }

        return $this->{$action}($request, $handler);
    }

    /**
     * @param string $phrase
     * @param string $separator
     * @return string
     */
    private function transformPhraseToCamelCase(string $phrase, string $separator = '-'): string
    {
        if (false !== strpos($phrase, $separator)) {
            return str_replace($separator, '', ucwords($phrase, $separator));
        }
        return $phrase;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getBaseUrl(ServerRequestInterface $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
    }
}