<?php

namespace Sergiors\Mapping;

/**
 * @param array $ls
 *
 * @return bool
 */
function array_multi_exists(array $ls)
{
    $head = key(array_slice($ls, 0, 1));
    return array_key_exists($head, $ls) && is_array($ls[$head]);
}
