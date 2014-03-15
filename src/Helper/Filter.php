<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Helper;

trait Filter
{
    public function filter($filter, $limit = null)
    {
        if (0 === $limit) {
            return null;
        }

        if ($this instanceof \Iterator) {
            $data = $this;
        } elseif (isset($this->data) && is_array($this->data)) {
            $data = $this->data;
        } else {
            throw new \RuntimeException('Can not filter this storage.');
        }

        $count = 0;
        $result = [];
        foreach ($data as $key => $value) {
            $pass = $this->server->getLanguage()->evaluate($filter, [
                'key' => $key,
                'value' => $value,
            ]);

            if ($pass) {
                $result[$key] = $value;
                $count++;

                if (null !== $limit && $count >= $limit) {
                    break;
                }
            }
        }

        return empty($result) ? null : $result;
    }
} 