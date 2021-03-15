<?php


namespace User\Middleware;


use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Router\RouteResult;
use Zend\Session\Container as SessionContainer;

/**
 * Class CsrfMiddleware
 * @package User\Middleware
 */
class CsrfMiddleware implements MiddlewareInterface
{
    const GUARD_ATTRIBUTE = 'csrf';

    protected array $allowedRoutesForSessionCreate = [
        'register'
    ];

    protected SessionContainer $sessionContainer;

    /**
     * CsrfMiddleware constructor.
     * @param SessionContainer $sessionContainer
     */
    public function __construct(SessionContainer $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        $routeName = $routeResult->getMatchedRouteName();

        //проверяем: нужна ли нам сессия и валидируем токен
        if (in_array($routeName, $this->allowedRoutesForSessionCreate)) {
            //возвращаем response, если токен не валидный
            if ($request->getMethod() == RequestMethodInterface::METHOD_POST
                && !$this->validateCsrf($request->getParsedBody()[static::GUARD_ATTRIBUTE])) {
                return new EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);
            }

            //генерируем токен и записываем его в сессию, если необходимо
            $this->generateCsrf();
        }

        return $handler->handle($request->withAttribute(
            static::class,
            $this->sessionContainer->offsetGet(static::class)));
    }

    /**
     * @param string|null $csrf
     * @return bool
     */
    protected function validateCsrf(?string $csrf): bool
    {
        //если токен отсутствует
        if (!$this->sessionContainer->offsetExists(static::class)) {
            return false;
        }

        //если токен в сессии не совпал с токеном из body
        return hash_equals($this->sessionContainer->offsetGet(static::class), $csrf);
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function generateCsrf(): void
    {
        if (!$this->sessionContainer->offsetGet(static::class)) {
            $this->sessionContainer->offsetSet(static::class, bin2hex(random_bytes(32)));
        }
    }

}