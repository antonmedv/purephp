<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Helper;

trait All
{
    /**
     * Return all data.
     *
     * @return array
     */
    public function all()
    {
        return iterator_to_array($this);
    }  
} 
