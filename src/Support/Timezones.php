<?php

namespace Electrik\Support;

class Timezones
{
    /**
     * @return array<string, string> identifier => label
     */
    public static function options(): array
    {
        $items = [];

        foreach (\DateTimeZone::listIdentifiers() as $identifier) {
            $items[$identifier] = $identifier;
        }

        return $items;
    }
}
