<?php

namespace DatamapsPHP\DTOs;

class Layer
{
    public readonly string $name;
    /** @var array<Marker> $markers */
    public readonly array $markers;

    /** @param array<Marker> $markers */
    private function __construct(string $name, array $markers)
    {
        $this->name = $name;
        $this->markers = $markers;
    }

    public static function createFromObject(\stdClass $object): Layer
    {
        $markers = [];
        foreach ($object->markers as $marker) {
            $markers[] = Marker::createFromObject($marker);
        }
        return new self(
            $object->name,
            $markers
        );
    }
}
