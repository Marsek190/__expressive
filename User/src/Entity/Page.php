<?php
declare(strict_types=1);

namespace User\Entity;


/**
 * Class Page
 * @package User\Entity
 */
class Page
{
    protected ?string $current;

    protected ?string $previous;

    /**
     * @return string|null
     */
    public function getCurrent(): ?string
    {
        return $this->current;
    }

    /**
     * @param string|null $current
     * @return Page
     */
    public function setCurrent(?string $current): Page
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    /**
     * @param string|null $previous
     * @return Page
     */
    public function setPrevious(?string $previous): Page
    {
        $this->previous = $previous;
        return $this;
    }
}
