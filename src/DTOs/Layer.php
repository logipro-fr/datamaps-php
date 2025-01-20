<?php

namespace DatamapsPHP\DTOs;

class Layer
{
    /** @param array<Marker> $markers */
    public function __construct(
        public readonly string $name,
        public readonly array $markers
    ) {
    }

    /** @param \stdClass&object{markers:array<mixed>,name:string} $object */
    public static function createFromObject(\stdClass $object): Layer
    {
        $markers = [];
        foreach ($object->markers as $marker) {
            /** @var \stdClass&object{point:array<float>,description:string,color:string} $marker */
            $markers[] = Marker::createFromObject($marker);
        }
        return new self(
            $object->name,
            $markers
        );
    }
}
