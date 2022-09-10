<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\ApiPlatform;

use ApiPlatform\Core\Serializer\ItemNormalizer;
use ArrayAccess;
use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class AlreadyObjectItemNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    /**
     * @param ItemNormalizer $itemNormalizer
     */
    public function __construct(private ItemNormalizer $itemNormalizer)
    {
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return mixed
     * @throws ExceptionInterface
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if (is_object($data) && !$data instanceof ArrayAccess) {
            return $data;
        }

        return $this->itemNormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * @return bool
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return $this->itemNormalizer->hasCacheableSupportsMethod();
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return string|int|bool|ArrayObject|array|float|null
     * @throws ExceptionInterface
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string|int|bool|ArrayObject|array|null|float
    {
        return $this->itemNormalizer->normalize($object, $format, $context);
    }

    /**
     * @param SerializerInterface $serializer
     * @return void
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->itemNormalizer->setSerializer($serializer);
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return bool
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->itemNormalizer->supportsDenormalization($data, $type, $format);
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return bool
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $this->itemNormalizer->supportsNormalization($data, $format);
    }
}
