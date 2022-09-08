<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition\Collector;

use FilesystemIterator;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinition;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use ReflectionException;
use RegexIterator;
use Throwable;

final class AttributeCollector implements CollectorInterface
{
    /** @var ResourceActionDefinition[] $definitions */
    private array $definitions = [];

    /**
     * @param string[] $directories
     * @throws ReflectionException
     */
    public function __construct(array $directories)
    {
        foreach ($directories as $directory) {
            foreach ($this->loadClassesInDirectory($directory) as $className => $classReflection) {
                foreach ($classReflection->getAttributes(ResourceAction::class) as $reflectionAttribute) {
                    /** @var ResourceAction $resourceAction */
                    $resourceAction = $reflectionAttribute->newInstance();
                    $this->definitions[] = ResourceActionDefinition::fromResourceActionAttribute(
                        $resourceAction,
                        $className
                    );
                }
            }
        }
    }

    /**
     * @return ResourceActionDefinition[]
     */
    public function getResourceActionDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * This method is copied from API Platform ReflectionClassRecursiveIterator
     *
     * @param string $directory
     * @return Generator<class-string, ReflectionClass<object>>
     * @throws ReflectionException
     */
    private function loadClassesInDirectory(string $directory): Generator
    {
        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/^.+\.php$/i',
            RecursiveRegexIterator::GET_MATCH
        );

        foreach ($iterator as $file) {
            $sourceFile = $file[0];

            if (!preg_match('(^phar:)i', $sourceFile)) {
                $sourceFile = realpath($sourceFile);
            }

            try {
                require_once $sourceFile;
            } catch (Throwable $t) {
                // invalid PHP file (example: missing parent class)
                continue;
            }

            $includedFiles[$sourceFile] = true;
        }

        $declared = array_merge(get_declared_classes(), get_declared_interfaces());
        foreach ($declared as $className) {
            $reflectionClass = new ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();
            if (isset($includedFiles[$sourceFile])) {
                yield $className => $reflectionClass;
            }
        }
    }
}
