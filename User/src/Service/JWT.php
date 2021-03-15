<?php
declare(strict_types=1);

namespace User\Service;


use Ramsey\Uuid\Uuid;
use Lcobucci\JWT\Configuration as JWTConfiguration;
use Lcobucci\JWT\Token as ParsedToken;
use Lcobucci\JWT\Validation\InvalidToken as InvalidTokenException;
use Core\Entity\User\AbstractRootEntity;

/**
 * Class JWT
 * @package User\Service
 */
class JWT
{
    const UUID_CLAIM = 'uuid';
    const USER_NAME_HEADER = 'name';

    protected JWTConfiguration $tokenConfiguration;

    /**
     * JWT constructor.
     * @param JWTConfiguration $tokenConfiguration
     */
    public function __construct(JWTConfiguration $tokenConfiguration)
    {
        $this->tokenConfiguration = $tokenConfiguration;
    }

    /**
     * @param AbstractRootEntity $userEntity
     * @return string
     */
    public function createRememberToken(AbstractRootEntity $userEntity): string
    {
        $now = new \DateTimeImmutable();
        $uuid = Uuid::uuid4()->toString();
        $token = $this->tokenConfiguration
            ->createBuilder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($now->modify('+1 month'))
            ->withHeader(static::USER_NAME_HEADER, $userEntity->getUserName())
            ->withClaim(static::UUID_CLAIM, $uuid)
            ->getToken(
                $this->tokenConfiguration->getSigner(),
                $this->tokenConfiguration->getSigningKey());

        return (string) $token;
    }

    /**
     * @param $parsedToken
     * @return void
     * @throws InvalidTokenException
     */
    public function verifyOrFailRememberToken(ParsedToken $parsedToken): void
    {
        $constraints = $this->tokenConfiguration->getValidationConstraints();
        $this->tokenConfiguration->getValidator()->assert($parsedToken, ...$constraints);
    }

    /**
     * @param mixed $token
     * @return ParsedToken
     */
    public function getParsedToken($token): ParsedToken
    {
        return $this->tokenConfiguration->getParser()->parse((string) $token);
    }
}