<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

class Factory
{
    static private $storages = [
        ArrayStorage::alias => ArrayStorage::class,
        LifetimeStorage::alias => LifetimeStorage::class,
        PriorityQueueStorage::alias => PriorityQueueStorage::class,
        QueueStorage::alias => QueueStorage::class,
        StackStorage::alias => StackStorage::class,
    ];

    public static function getClassByAlias($alias)
    {
        if (isset(self::$storages[$alias])) {
            return self::$storages[$alias];
        } else {
            throw new \RuntimeException("There are no `$alias` storage.");
        }
    }
} 