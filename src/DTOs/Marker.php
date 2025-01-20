<?php

namespace DatamapsPHP\DTOs;

class Marker
{
    /** @param array<float> $point */
    public function __construct(
        public readonly array $point,
        public readonly string $description,
        public readonly string $color
    ) {
    }

    /** @param \stdClass&object{point:array<float>,description:string,color:string} $object */
    public static function createFromObject(\stdClass $object): Marker
    {
        return new self(
            $object->point,
            $object->description,
            $object->color
        );
    }
}
