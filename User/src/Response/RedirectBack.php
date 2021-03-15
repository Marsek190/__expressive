<?php
declare(strict_types=1);

namespace User\Response;


use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Skeleton\Entity\Page;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Session\Container as SessionContainer;

/**
 * Class RedirectBack
 * @package User\Response
 */
final class RedirectBack extends RedirectResponse
{
    const HOME_PAGE_URL = '/';

    /**
     * RedirectBack constructor.
     * @param ServerRequestInterface $request
     * @param int $status
     * @param array $headers
     */
    public function __construct(ServerRequestInterface $request, int $status = StatusCodeInterface::STATUS_FOUND, array $headers = [])
    {
        parent::__construct($this->getPreviousPage($request), $status, $headers);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getPreviousPage(ServerRequestInterface $request): string
    {
        /** @var SessionContainer|null $container */
        $container = $request->getAttribute(SessionContainer::class);
        if (is_null($container)) {
            return static::HOME_PAGE_URL;
        }
        /** @var Page $page */
        $page = $container->offsetGet(Page::class);
        return $page->getPrevious();
    }
}