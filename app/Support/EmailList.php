<?php

namespace App\Support;

class EmailList
{
    /**
     * Convierte "a@x.com, b@y.com; c@z.com" en ['a@x.com','b@y.com','c@z.com'] (sin repetidos).
     */
    public static function parse(?string $texto): array
    {
        $partes = preg_split('/[,;\s]+/', trim((string) $texto), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_map('strtolower', $partes)));
    }
}
