<?php
declare(strict_types=1);

namespace User\Service\Mail;


use User\Entity\Mail\EmailRendererInterface;
use User\Entity\Mail\SenderConfiguration;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * Class RenderedEmail
 * @package User\Service\Mail
 */
final class RenderedEmail implements EmailRendererInterface
{
    protected TemplateRendererInterface $templateRenderer;

    protected string $template;

    /**
     * RenderedEmail constructor.
     * @param TemplateRendererInterface $templateRenderer
     * @param string $template
     */
    public function __construct(TemplateRendererInterface $templateRenderer, string $template)
    {
        $this->templateRenderer = $templateRenderer;
        $this->template = $template;
    }

    /**
     * @param SenderConfiguration $configuration
     * @return string
     */
    public function render(SenderConfiguration $configuration): string
    {
        return $this->templateRenderer->render(
            $this->template,
            array_merge($configuration->getTemplateVariables(), ['subject' => $configuration->getSubject()]));
    }
}