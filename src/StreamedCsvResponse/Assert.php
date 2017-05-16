<?php

namespace Issei\StreamedCsvResponse;

/**
 * Assertion.
 *
 * {@internal Don't use this in user-land code }}
 *
 * @author Issei Murasawa <issei.m7@gmail.com>
 */
final class Assert
{
    /**
     * Throws an \InvalidArgumentException if given value is not iterable.
     *
     * @param mixed  $value
     * @param string $message
     *
     * @return bool
     */
    public static function isIterable($value, $message)
    {
        if (!$value instanceof \Traversable && !is_array($value)) {
            throw new \InvalidArgumentException($message);
        }
    }

    private function __construct() {}
}
