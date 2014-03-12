<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Store;

class StoreFactory
{
    static private $stores = [
        ArrayStore::alias => ArrayStore::class,
        LifetimeStore::alias => LifetimeStore::class,
        PriorityQueueStore::alias => PriorityQueueStore::class,
        QueueStore::alias => QueueStore::class,
        StackStore::alias => StackStore::class,
    ];

    public static function getClassByAlias($alias)
    {
        if (isset(self::$stores[$alias])) {
            return self::$stores[$alias];
        } else {
            throw new \RuntimeException("There are no `$alias` store.");
        }
    }
} 