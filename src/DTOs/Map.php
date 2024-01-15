<?php

namespace DatamapsPHP\DTOs;

class Map
{
    /**
     * @param array<array<float>> $bounds
     * @param array<Layer> $layers
     */
    public function __construct(
        public readonly string $mapId,
        public readonly array $bounds,
        public readonly string $createdAt,
        public readonly array $layers
    ) {
    }

    public static function createFromObject(\stdClass $object): Map
    {
        $layers = [];
        foreach ($object->layers as $layer) {
            $layers[] = Layer::createFromObject($layer);
        }
        return new self(
            $object->mapId,
            $object->bounds,
            $object->createdAt,
            $layers
        );
    }
}
