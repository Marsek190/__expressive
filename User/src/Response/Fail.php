<?php
declare(strict_types=1);

namespace User\Response;


use Fig\Http\Message\StatusCodeInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class Fail
 * @package User\Response
 */
final class Fail extends JsonResponse
{
    private function __construct(array $data, $status = StatusCodeInterface::STATUS_BAD_REQUEST)
    {
        parent::__construct(
            array_merge($data, ['status' => false]),
            $status,
            $headers = [],
            $encodingOptions = self::DEFAULT_JSON_FLAGS);
    }

    public static function fromArrayMessages(array $messages, $status = StatusCodeInterface::STATUS_BAD_REQUEST): Fail
    {
        return new static(compact('messages'), $status);
    }

    public static function fromStringMessage(string $message, $status = StatusCodeInterface::STATUS_BAD_REQUEST): Fail
    {
        return new static(compact('message'), $status);
    }
}
