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

    /** @param \stdClass&object{layers:array<\stdClass>,mapId:string,bounds:array<array<float>>,createdAt:string} $object */
    public static function createFromObject(\stdClass $object): Map
    {
        $layers = [];
        foreach ($object->layers as $layer) {
            /** @var \stdClass&object{markers:array<mixed>,name:string} $layer */
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
