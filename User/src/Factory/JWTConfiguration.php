<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Zend\ServiceManager\Factory\FactoryInterface;

class JWTConfiguration implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')[static::class];

        return Configuration::forSymmetricSigner(
            new Sha256(),
            new Key($config['authKey'])
        );
    }
}