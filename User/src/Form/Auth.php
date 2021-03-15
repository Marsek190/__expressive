<?php
declare(strict_types=1);

namespace User\Form;


use User\Middleware\CsrfMiddleware;
use Zend\Form\Form;
use Zend\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilter;
use Fig\Http\Message\RequestMethodInterface;

class Auth extends Form
{
    public function __construct(InputFilter $inputFilter, HydratorInterface $hydrator)
    {
        parent::__construct(static::class);

        $this->setInputFilter($inputFilter);
        $this->setHydrator($hydrator);
        $this->setAttribute(Registration::METHOD_ATTRIBUTE, RequestMethodInterface::METHOD_POST);

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements(): Auth
    {
        $this->add([
            'type'  => 'text',
            'name' => Registration::EMAIL_PROPERTY,
            'attributes' => [
                'id' => Registration::EMAIL_PROPERTY
            ],
            'options' => [
                'label' => 'Ваш E-mail',
            ],
        ]);
        $this->add([
            'type'  => Registration::PASSWORD_PROPERTY,
            'name' => Registration::PASSWORD_PROPERTY,
            'attributes' => [
                'id' => Registration::PASSWORD_PROPERTY
            ],
            'options' => [
                'label' => 'Пароль',
            ],
        ]);
        $this->add([
            'type'  => 'checkbox',
            'name' => Registration::REMEMBER_ME_PROPERTY,
            'attributes' => [
                'id' => Registration::REMEMBER_ME_PROPERTY
            ],
            'options' => [
                'label' => 'Запомнить меня',
            ],
        ]);
        $this->add([
            'type'  => CsrfMiddleware::GUARD_ATTRIBUTE,
            'name' => CsrfMiddleware::GUARD_ATTRIBUTE,
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        return $this;
    }

    protected function addInputFilter(): Auth
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'     => Registration::EMAIL_PROPERTY,
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => Registration::PASSWORD_PROPERTY,
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 6,
                        'max' => 64
                    ],
                ],
            ],
        ]);

        return $this;
    }
}