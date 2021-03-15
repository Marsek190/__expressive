<?php
declare(strict_types=1);

namespace User\Response;


use Fig\Http\Message\StatusCodeInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class Success
 * @package User\Response
 */
final class Success extends JsonResponse
{
    public function __construct(array $data = [])
    {
        parent::__construct(
            array_merge($data, ['status' => 'ok']),
            $status = StatusCodeInterface::STATUS_OK,
            $headers = [],
            $encodingOptions = self::DEFAULT_JSON_FLAGS);
    }
}