<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceModulesContainerInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class ViewsConfig
{
    private string $namespaceNotFoundErrorMessage;
    private ?ModelNamespaceProviderInterface $modelNamespaceProvider;
    private ?ViewNamespaceModulesContainerInterface $namespaceModulesContainer;

    public function __construct()
    {
        $this->modelNamespaceProvider = null;
        $this->namespaceModulesContainer = null;
        $this->namespaceNotFoundErrorMessage = 'Model namespace cannot be resolved';
    }

    //// Getters:

    public function getNamespaceNotFoundErrorMessage(): string
    {
        return $this->namespaceNotFoundErrorMessage;
    }

    public function getModelNamespaceProvider(): ?ModelNamespaceProviderInterface
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

    public function setModelNamespaceProvider(?ModelNamespaceProviderInterface $modelNamespaceProvider): self
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
