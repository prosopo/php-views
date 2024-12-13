<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

class View implements ViewInterface
{
    private TemplateProviderInterface $templateProvider;

    final public function __construct(TemplateProviderInterface $template_provider)
    {
        $this->templateProvider = $template_provider;

        $this->setCustomDefaults();
    }

    public function getTemplate(): string
    {
        return $this->templateProvider->getTemplate($this);
    }

    protected function setCustomDefaults(): void
    {
    }
}
