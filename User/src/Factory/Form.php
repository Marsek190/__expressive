<?php


namespace User\Factory;


use Interop\Container\ContainerInterface;
use User\Form\Registration;
use Zend\Hydrator\ClassMethods;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Form\RestorePassword;
use User\Form\Auth as AuthForm;

class Form implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        switch ($requestedName) {
            case Registration::class:
            case RestorePassword::class:
            case AuthForm::class:
                $form = new $requestedName(
                    $container->get(InputFilter::class),
                    $container->get(ClassMethods::class)
                );
                break;
            default:
                $form = new $requestedName();
                break;
        }

        return $form;
    }
}