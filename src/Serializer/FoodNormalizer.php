<?php

namespace App\Serializer;

use App\Entity\Food;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FoodNormalizer implements NormalizerInterface
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
            $data['image'] = 'http://localhost:8000/images/' . $data['image'];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Food;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Food::class => true,
        ];
    }
}
