<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Helper;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

trait Filter
{
    /**
     * Write filter string in ExpressionLanguage.
     * In expression you can use next variables:
     *   `key` - current element key.
     *   `value` - current element value.
     * 
     * Examples:
     *   value in ['good_customers', 'collaborator']
     *   key > 100 and value not in ["misc"]
     * 
     * @link http://symfony.com/doc/current/components/expression_language/introduction.html
     * @param string $filter 
     * @param null|int $limit How many elements to search.
     * @return array|null
     */
    public function filter($filter, $limit = null)
    {
        static $language = null;
        
        if (null === $language) {
            $language = new ExpressionLanguage();
        }

        $count = 0;
        $result = [];
        foreach ($this as $key => $value) {
            $pass = $language->evaluate($filter, [
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
