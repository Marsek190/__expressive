<?php
declare(strict_types=1);

namespace User\Exception;


/**
 * Class FieldValidationError
 * @package User\Exception
 */
final class FieldValidationError extends \InvalidArgumentException
{
    private string $validatorError;

    private string $validatorFieldName;

    /**
     * FieldValidationError constructor.
     * @param string $message
     * @param string $fieldName
     */
    public function __construct(string $message, string $fieldName)
    {
        $this->setValidatorError($message);
        $this->setValidatorFieldName($fieldName);

        parent::__construct($message, 0, null);
    }

    /**
     * @return string
     */
    public function getValidatorError(): string
    {
        return $this->validatorError;
    }

    /**
     * @param string $validatorError
     * @return FieldValidationError
     */
    public function setValidatorError(string $validatorError): FieldValidationError
    {
        $this->validatorError = $validatorError;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidatorFieldName(): string
    {
        return $this->validatorFieldName;
    }

    /**
     * @param string $validatorFieldName
     * @return FieldValidationError
     */
    public function setValidatorFieldName(string $validatorFieldName): FieldValidationError
    {
        $this->validatorFieldName = $validatorFieldName;
        return $this;
    }
}