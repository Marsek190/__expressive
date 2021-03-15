<?php
declare(strict_types=1);

namespace User\Form;


use Fig\Http\Message\RequestMethodInterface;
use User\Middleware\CsrfMiddleware;
use Zend\Form\Form;
use Zend\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilter;

/**
 * Class RestorePassword
 * @package User\Form
 */
class RestorePassword extends Form
{
    const ID_PROPERTY = 'user_id';
    const SECURE_CODE_PROPERTY = 'secure_code';

    /**
     * RestorePassword constructor.
     * @param InputFilter $inputFilter
     * @param HydratorInterface $hydrator
     */
    public function __construct(InputFilter $inputFilter, HydratorInterface $hydrator)
    {
        parent::__construct(static::class);

        $this->setInputFilter($inputFilter);
        $this->setHydrator($hydrator);
        $this->setAttribute(Registration::METHOD_ATTRIBUTE, RequestMethodInterface::METHOD_POST);

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return RestorePassword
     */
    protected function addElements(): RestorePassword
    {
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
            'type'  => Registration::PASSWORD_PROPERTY,
            'name' => Registration::CONFIRM_PASSWORD_PROPERTY,
            'attributes' => [
                'id' => Registration::CONFIRM_PASSWORD_PROPERTY
            ],
            'options' => [
                'label' => 'Подтвердите пароль',
            ],
        ]);
        $this->add([
            'type'  => 'number',
            'name' => static::ID_PROPERTY,
            'attributes' => [
                'id' => static::ID_PROPERTY
            ],
            'options' => [
                'label' => 'Идентификатор пользователя',
            ],
        ]);
        $this->add([
            'type'  => 'text',
            'name' => static::SECURE_CODE_PROPERTY,
            'attributes' => [
                'id' => static::SECURE_CODE_PROPERTY
            ],
            'options' => [
                'label' => 'Код подтверждения',
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

    /**
     * @return RestorePassword
     */
    protected function addInputFilter(): RestorePassword
    {
        $inputFilter = $this->getInputFilter();

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
        $inputFilter->add([
            'name'     => Registration::CONFIRM_PASSWORD_PROPERTY,
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => Registration::PASSWORD_PROPERTY,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'     => static::ID_PROPERTY,
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' => 'Digits'
                ]
            ]
        ]);
        $inputFilter->add([
            'name'     => static::SECURE_CODE_PROPERTY,
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
            ]
        ]);

        return $this;
    }
}