<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;

class Sendmail extends Factory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return static::create([
            'type' => 'sendmail',
            'options' => [
            ]
        ]);
    }
}