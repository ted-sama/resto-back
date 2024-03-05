<?php

namespace App\Serializer;

use App\Entity\Category;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // Modifier le chemin de l'image pour qu'il soit absolu
        if (isset($data['image'])) {
            $data['image'] = $_ENV["IMG_URL"] . $data['image'];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Category;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Category::class => true,
        ];
    }
}
