<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;

final class ObjectReaderWithRendering implements ObjectReaderInterface
{
    private ObjectReaderInterface $objectReader;
    private ModelRendererInterface $modelRenderer;

    public function __construct(
        ObjectReaderInterface $objectReader,
        ModelRendererInterface $viewRenderer
    ) {
        $this->objectReader = $objectReader;
        $this->modelRenderer = $viewRenderer;
    }

    public function getObjectVariables(object $instance): array
    {
        $variables = $this->objectReader->getObjectVariables($instance);

        return $this->renderInnerModels($variables);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return array<string,mixed>
     */
    protected function renderInnerModels(array $variables): array
    {
        return array_map(
            function ($item) {
                return $this->renderIfModel($item);
            },
            $variables
        );
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    protected function renderIfModel($item)
    {
        if (true === ( $item instanceof TemplateModelInterface )) {
            $item = $this->modelRenderer->renderModel($item);
        } elseif (
            true === is_array($item) &&
            false === is_callable($item)
        ) {
            // @phpstan-ignore-next-line
            $item = $this->renderInnerModels($item);
        }

        return $item;
    }
}
