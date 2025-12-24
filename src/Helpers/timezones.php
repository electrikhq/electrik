<?php

if (!function_exists('timezones')) {
    function timezones()
    {
        $tzs = \DateTimeZone::listIdentifiers();
        $items = [];

        foreach ($tzs as $key => $value) {
            $items[$value] = $value;
        }

        return $items;
    }
}

