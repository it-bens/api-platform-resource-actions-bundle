<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Docs;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use Console_Table;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionCollection;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionCommandMetadata;
use ITB\ApiPlatformResourceActionsBundle\Docs\OpenApiFactoryException\PathNotfoundException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    /**
     * @param OpenApiFactoryInterface $decorated
     * @param ActionCollection $actionCollection
     * @param OperationPathResolverInterface $operationPathResolver
     */
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private ActionCollection $actionCollection,
        private OperationPathResolverInterface $operationPathResolver
    ) {
    }

    /**
     * @param array<string, mixed> $context
     * @return OpenApi
     * @throws CompileTimeExceptionInterface
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        foreach ($this->actionCollection->getActions() as $action) {
            /** @phpstan-ignore-next-line */
            $path = $this->operationPathResolver->resolveOperationPath(
                $action->getResourceName(),
                $action->getOperationData(),
                OperationType::ITEM,
                $action->getOperationName()
            );
            $apiPath = str_replace('.{_format}', '', $path);

            $pathItem = $openApi->getPaths()->getPath($apiPath);
            if (null === $pathItem) {
                throw PathNotfoundException::create($apiPath);
            }
            $patchOperation = $pathItem->getPatch();

            $description = '---' . PHP_EOL . PHP_EOL;
            $description .= '## Actions' . PHP_EOL;
            $description .= $this->buildActionTable($action->getResource());

            $openApi->getPaths()->addPath(
                $apiPath,
                $pathItem->withPatch(
                /** @phpstan-ignore-next-line */
                    $patchOperation->withDescription(
                    /** @phpstan-ignore-next-line */
                        $patchOperation->getDescription() . PHP_EOL . PHP_EOL . $description
                    )
                )
            );
        }

        return $openApi;
    }

    /**
     * @param string $resource
     * @return string
     */
    private function buildActionTable(string $resource): string
    {
        $table = new Console_Table();
        $headers = ['Action', 'Command', 'Payload', 'Description'];
        $table->setHeaders($headers);

        foreach ($this->actionCollection->getActionsForResource($resource) as $action) {
            $properties = $this->getPayloadProperties($action->getCommandMetadata(), $resource);
            $description = $action->getDescription() ?? '';

            $table->addRow([$action->getName(), $action->getCommandClass(), $properties, $description]);
        }

        $renderedTable = $table->getTable();
        $lineLength = array_sum($table->_cell_lengths) + count($headers) * 3 + 2;
        // Replace characters
        $openApiCompatibleTable = str_replace('+', '|', $renderedTable);
        // Remove first line
        $openApiCompatibleTable = substr($openApiCompatibleTable, (int)$lineLength);
        // Remove last line
        $openApiCompatibleTable = substr($openApiCompatibleTable, 0, -(int)$lineLength);

        return $openApiCompatibleTable;
    }

    /**
     * @param ActionCommandMetadata $commandMetadata
     * @param string $resource
     * @return string
     */
    private function getPayloadProperties(ActionCommandMetadata $commandMetadata, string $resource): string
    {
        $properties = '';
        foreach ($commandMetadata->getConstructorParameters() as $parameter) {
            if ($resource === (string)$parameter->getType()) {
                continue;
            }

            $type = (string)$parameter->getType();
            if (null !== $parameter->getType() && $parameter->getType()->allowsNull()) {
                $type .= '|null';
            }

            $properties .= sprintf('- __%s__ (%s)', $parameter->getName(), $type) . PHP_EOL;
        }

        return $properties;
    }
}
