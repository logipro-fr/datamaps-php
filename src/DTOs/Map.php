<?php

namespace DatamapsPHP\DTOs;

class Map
{
    public readonly string $mapId;
    /** @var array<array<float>> $bounds */
    public readonly array $bounds;
    public readonly string $createdAt;
    /** @var array<Layer> $layers */
    public readonly array $layers;

    /**
     * @param array<array<float>> $bounds
     * @param array<Layer> $layers
     */
    private function __construct(string $mapId, array $bounds, string $createdAt, array $layers)
    {
        $this->mapId = $mapId;
        $this->bounds = $bounds;
        $this->createdAt = $createdAt;
        $this->layers = $layers;
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
