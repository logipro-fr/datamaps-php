<?php

namespace DatamapsPHP\DTOs;

class Marker
{
    /** @var array<float> $point */
    public readonly array $point;
    public readonly string $description;
    public readonly string $color;

    /** @param array<float> $point */
    private function __construct(array $point, string $description, string $color)
    {
        $this->point = $point;
        $this->description = $description;
        $this->color = $color;
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
