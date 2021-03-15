<?php
declare(strict_types=1);

namespace User\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Entity\Page;
use Entity\User;
use User\Response\RedirectBack;
use Zend\Session\Container as SessionContainer;

/**
 * Class PreviousPageMiddleware
 * @package User\Middleware
 */
class PreviousPageMiddleware implements MiddlewareInterface
{
    protected SessionContainer $sessionContainer;

    /**
     * PreviousPageMiddleware constructor.
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
        if (is_null($request->getAttribute(User::class))) {
            return $handler->handle($request);
        }

        /** 
         * @var Page|null $page 
         */
        $page = $this->sessionContainer->offsetGet(Page::class);
        
        if (is_null($page)) {
            $page = (new Page())
                ->setCurrent($request->getUri()->getPath())
                ->setPrevious(RedirectBack::HOME_PAGE_URL);
            $this->sessionContainer->offsetSet(Page::class, $page);
            
            return $handler->handle($request->withAttribute(SessionContainer::class, $this->sessionContainer));
        }
        $this->sessionContainer->offsetSet(Page::class, $page->setPrevious($page->getCurrent())->setCurrent($request->getUri()->getPath()));

        return $handler->handle($request->withAttribute(SessionContainer::class, $this->sessionContainer));
    }
}
