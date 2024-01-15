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

    public static function createFromObject(\stdClass $object): Marker
    {
        return new self(
            $object->point,
            $object->description,
            $object->color
        );
    }
}
