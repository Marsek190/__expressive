<?php
declare(strict_types=1);

namespace User\Service;


use Core\Entity\User\RestorePassword;
use User\Exception\FieldValidationError;
use User\Exception\UserExistsError;
use User\Service\RestorePassword as RestorePasswordService;
use User\Service\User as UserService;
use User\Service\Mail\SenderResettingEmailMessage;
use Ramsey\Uuid\Uuid;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RequestForChangePassword
 * @package User\Service
 */
class RequestForChangePassword
{
    protected UserService $userService;

    protected RestorePasswordService $restorePasswordService;

    protected SenderResettingEmailMessage $emailSender;

    protected UrlHelper $urlHelper;

    protected string $userEmail;

    protected string $baseUrl;

    /**
     * RequestForChangePassword constructor.
     * @param UserService $userService
     * @param RestorePasswordService $restorePasswordService
     * @param SenderResettingEmailMessage $emailSender
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        UserService $userService,
        RestorePasswordService $restorePasswordService,
        SenderResettingEmailMessage $emailSender,
        UrlHelper $urlHelper)
    {
        $this->userService = $userService;
        $this->restorePasswordService = $restorePasswordService;
        $this->emailSender = $emailSender;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @return void
     * @throws UserExistsError
     */
    public function requestForChangePassword(): void
    {
        /** @var \User\Entity\UserRestorePassword $user */
        $user = $this->userService->getUserRestorePasswordByEmail($this->userEmail);
        if (is_null($user)) {
            throw new UserExistsError(sprintf(" %s", $this->userEmail));
        }
        $currentRestoredTimestamp = time();
        $restorePassword = (new RestorePassword())
            ->setId($user->getRestoreId())
            ->setUserId($user->getId())
            ->setUpdated($currentRestoredTimestamp)
            ->setSecureCode($this->createSecureCode());
        // если пользователь до этого уже сбрасывал пароль
        if (is_null($user->getCreated())) {
            //
            $this->restorePasswordService->saveOrFail($restorePassword->setCreated($currentRestoredTimestamp));
        } else {
            // иначе апдейтим запись
            $this->restorePasswordService->updateOrFailSecureCodeAndUpdatedFields($restorePassword);
        }

        $this->emailSender->send($user->setResettingLink($this->createResettingLink($restorePassword)));
    }

    /**
     * @param RestorePassword $restorePassword
     * @return string
     */
    protected function createResettingLink(RestorePassword $restorePassword): string
    {
        return $this->baseUrl . $this->urlHelper->generate('password-change', ['action' => 'change'], [
            'user_id' => $restorePassword->getUserId(),
            'secure_code' => $restorePassword->getSecureCode()
        ]);
    }

    /**
     * @return string
     */
    protected function createSecureCode(): string
    {
        return bin2hex(random_bytes(64)) . Uuid::uuid4()->toString();
    }

    /**
     * @param string $userEmail
     * @return RequestForChangePassword
     * @throws FieldValidationError
     */
    public function setUserEmail(string $userEmail): RequestForChangePassword
    {
        if (false === filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new FieldValidationError("Invalid email address", $userEmail);
        }
        $this->userEmail = $userEmail;
        return $this;
    }

    /**
     * @param string $baseUrl
     * @return RequestForChangePassword
     */
    public function setBaseUrl(string $baseUrl): RequestForChangePassword
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
}