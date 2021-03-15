<?php
declare(strict_types=1);

namespace User\Form;


use User\Middleware\CsrfMiddleware;
use Zend\Form\Form;
use Zend\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilter;
use Fig\Http\Message\RequestMethodInterface;

class Registration extends Form
{
    const METHOD_ATTRIBUTE = 'method';

    const EMAIL_PROPERTY = 'email';
    const USER_NAME_PROPERTY = 'user_name';
    const PASSWORD_PROPERTY = 'password';
    const CONFIRM_PASSWORD_PROPERTY = 'confirm_password';
    const REMEMBER_ME_PROPERTY = 'remember_me';

    public function __construct(InputFilter $inputFilter, HydratorInterface $hydrator)
    {
        parent::__construct(static::class);

        $this->setInputFilter($inputFilter);
        $this->setHydrator($hydrator);
        $this->setAttribute(static::METHOD_ATTRIBUTE, RequestMethodInterface::METHOD_POST);

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements(): Registration
    {
        $this->add([
            'type'  => 'text',
            'name' => static::EMAIL_PROPERTY,
            'attributes' => [
                'id' => static::EMAIL_PROPERTY
            ],
            'options' => [
                'label' => 'Ваш E-mail',
            ],
        ]);
        $this->add([
            'type'  => 'text',
            'name' => static::USER_NAME_PROPERTY,
            'attributes' => [
                'id' => static::USER_NAME_PROPERTY
            ],
            'options' => [
                'label' => 'Имя пользователя',
            ],
        ]);
        $this->add([
            'type'  => static::PASSWORD_PROPERTY,
            'name' => static::PASSWORD_PROPERTY,
            'attributes' => [
                'id' => static::PASSWORD_PROPERTY
            ],
            'options' => [
                'label' => 'Пароль',
            ],
        ]);
        $this->add([
            'type'  => static::PASSWORD_PROPERTY,
            'name' => static::CONFIRM_PASSWORD_PROPERTY,
            'attributes' => [
                'id' => static::CONFIRM_PASSWORD_PROPERTY
            ],
            'options' => [
                'label' => 'Подтвердите пароль',
            ],
        ]);
        $this->add([
            'type'  => 'checkbox',
            'name' => static::REMEMBER_ME_PROPERTY,
            'attributes' => [
                'id' => static::REMEMBER_ME_PROPERTY
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

    protected function addInputFilter(): Registration
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name'     => static::EMAIL_PROPERTY,
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
            'name'     => static::USER_NAME_PROPERTY,
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 128
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => static::PASSWORD_PROPERTY,
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
        $inputFilter->add([
            'name'     => static::CONFIRM_PASSWORD_PROPERTY,
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => static::PASSWORD_PROPERTY,
                    ],
                ],
            ],
        ]);

        return $this;
    }
}