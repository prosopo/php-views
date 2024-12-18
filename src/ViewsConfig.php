<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Model\ModelNamespaceResolverInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceModulesContainerInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class ViewsConfig
{
    private string $namespaceNotFoundErrorMessage;
    private string $wrongModelErrorMessage;
    private ?ModelNamespaceResolverInterface $modelNamespaceProvider;
    private ?ViewNamespaceModulesContainerInterface $namespaceModulesContainer;

    public function __construct()
    {
        $this->modelNamespaceProvider = null;
        $this->namespaceModulesContainer = null;
        $this->namespaceNotFoundErrorMessage = 'Model namespace cannot be resolved';
        $this->wrongModelErrorMessage = 'Passed Model does not implement TemplateModelInterface';
    }

    //// Getters:

    public function getNamespaceNotFoundErrorMessage(): string
    {
        return $this->namespaceNotFoundErrorMessage;
    }

    public function getWrongModelErrorMessage(): string
    {
        return $this->wrongModelErrorMessage;
    }

    public function getModelNamespaceProvider(): ?ModelNamespaceResolverInterface
    {
        return $this->modelNamespaceProvider;
    }

    public function getNamespaceModulesContainer(): ?ViewNamespaceModulesContainerInterface
    {
        return $this->namespaceModulesContainer;
    }

    //// Setters:

    public function setNamespaceNotFoundErrorMessage(string $namespaceNotFoundErrorMessage): self
    {
        $this->namespaceNotFoundErrorMessage = $namespaceNotFoundErrorMessage;

        return $this;
    }

    public function setWrongModelErrorMessage(string $wrongModelErrorMessage): self
    {
        $this->wrongModelErrorMessage = $wrongModelErrorMessage;

        return $this;
    }

    public function setModelNamespaceProvider(?ModelNamespaceResolverInterface $modelNamespaceProvider): self
    {
        $this->modelNamespaceProvider = $modelNamespaceProvider;

        return $this;
    }

    public function setNamespaceModulesContainer(
        ?ViewNamespaceModulesContainerInterface $namespaceModulesContainer
    ): self {
        $this->namespaceModulesContainer = $namespaceModulesContainer;

        return $this;
    }
}
