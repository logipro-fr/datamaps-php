<?php

namespace DatamapsPHP;

class DatamapsClientFactory
{
    public static function make(): DatamapsClient
    {
        return new DatamapsClient();
    }
}
