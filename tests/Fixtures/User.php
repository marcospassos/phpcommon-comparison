<?php

/**
 * This file is part of the phpcommon/comparison package.
 *
 * (c) Marcos Passos <marcos@marcospassos.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace PhpCommon\Comparison\Tests\Fixtures;

use PhpCommon\Comparison\Equatable;
use PhpCommon\Comparison\Hashable;

/**
 * @since  1.0
 *
 * @author Marcos Passos <marcos@croct.com>
 */
class User implements Hashable
{
    /**
     * @var mixed
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Equatable $other)
    {
        if (!$other instanceof self || self::class !== get_class($other)) {
            return false;
        }

        return $this->id === $other->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        return $this->id;
    }
}
