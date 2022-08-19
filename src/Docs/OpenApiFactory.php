<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;
use Console_Table;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMap;
use ITB\ApiPlatformUpdateActionsBundle\Controller\Controller;
use ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException\ResourceActionDocumentationFailedException;
use ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException\ResourceMetadataRetrievalFailedException;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Request\Request;
use ReflectionClass;
use ReflectionException;
use Throwable;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    /**
     * @param OpenApiFactoryInterface $decorated
     * @param ResourceActionCommandMap $resourceActionCommandMap
     * @param ResourceActionDescriptionMap $resourceActionDescriptionMap
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @param OperationPathResolverInterface $operationPathResolver
     */
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private ResourceActionCommandMap $resourceActionCommandMap,
        private ResourceActionDescriptionMap $resourceActionDescriptionMap,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        private OperationPathResolverInterface $operationPathResolver
    ) {
    }

    /**
     * @param array $context
     * @return OpenApi
     * @throws CompileTimeExceptionInterface
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        foreach ($this->resourceActionCommandMap->getResources() as $resource) {
            try {
                $metadata = $this->resourceMetadataFactory->create($resource);
            } catch (ResourceClassNotFoundException $exception) {
                throw ResourceMetadataRetrievalFailedException::create($resource, $exception);
            }

            foreach ($metadata->getItemOperations() as $operationName => $operationData) {
                if (!array_key_exists('input', $operationData)
                    || !is_array($operationData['input'])
                    || !array_key_exists('class', $operationData['input'])
                    || Request::class !== $operationData['input']['class']) {
                    continue;
                }

                if (!array_key_exists('controller', $operationData)
                    || Controller::class !== $operationData['controller']) {
                    continue;
                }

                $path = $this->operationPathResolver->resolveOperationPath(
                    $metadata->getShortName(),
                    $operationData,
                    OperationType::ITEM,
                    $operationName
                );
                $apiPath = str_replace('.{_format}', '', $path);

                $pathItem = $openApi->getPaths()->getPath($apiPath);
                $patchOperation = $pathItem->getPatch();

                $description = '---' . PHP_EOL . PHP_EOL;
                $description .= '## Actions' . PHP_EOL;
                $description .= $this->buildActionTable($resource);

                $openApi->getPaths()->addPath(
                    $apiPath,
                    $pathItem->withPatch(
                        $patchOperation->withDescription(
                            $patchOperation->getDescription() . PHP_EOL . PHP_EOL . $description
                        )
                    )
                );
            }
        }

        return $openApi;
    }

    /**
     * @param string $resource
     * @return string
     * @throws CompileTimeExceptionInterface
     */
    private function buildActionTable(string $resource): string
    {
        $table = new Console_Table();
        $headers = ['Action', 'Command', 'Payload', 'Description'];
        $table->setHeaders($headers);

        foreach ($this->resourceActionCommandMap->getActionsForResource($resource) as $action) {
            try {
                $commandClass = $this->resourceActionCommandMap->getCommandClassForResourceAction($resource, $action);
                // The properties of the command class with their name and type accumulated in one string.
                $properties = $this->getPayloadProperties($commandClass, $resource);
                $description = $this->resourceActionDescriptionMap->getDescriptionForResourceAction($resource, $action);
            } catch (Throwable $exception) {
                throw ResourceActionDocumentationFailedException::create($resource, $action, $exception);
            }

            $table->addRow([$action, $commandClass, $properties, $description]);
        }

        $renderedTable = $table->getTable();
        $lineLength = array_sum($table->_cell_lengths) + count($headers) * 3 + 2;
        // Replace characters
        $openApiCompatibleTable = str_replace('+', '|', $renderedTable);
        // Remove first line
        $openApiCompatibleTable = substr($openApiCompatibleTable, $lineLength);
        // Remove last line
        $openApiCompatibleTable = substr($openApiCompatibleTable, 0, -$lineLength);

        return $openApiCompatibleTable;
    }

    /**
     * @param string $commandClass
     * @param string $resource
     * @return string
     * @throws ReflectionException
     */
    private function getPayloadProperties(string $commandClass, string $resource): string
    {
        $commandReflection = new ReflectionClass($commandClass);
        $commandConstructor = $commandReflection->getConstructor();

        $properties = '';
        foreach ($commandConstructor->getParameters() as $parameter) {
            if ($resource === (string)$parameter->getType()) {
                continue;
            }

            $type = (string)$parameter->getType();
            if ($parameter->getType()->allowsNull()) {
                $type .= '|null';
            }

            $properties .= sprintf('- __%s__ (%s)', $parameter->getName(), $type) . PHP_EOL;
        }

        return $properties;
    }
}