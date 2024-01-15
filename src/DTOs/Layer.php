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
