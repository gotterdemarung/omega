<?php

namespace Omega\Cache;


interface CacheInterface extends \ArrayAccess {

    /**
     * Returns clone of current CacheInterface with
     * adjusted time to live
     *
     * @param int $desiredTtl
     *
     * @return CacheInterface
     */
    public function cloneWithTtl($desiredTtl);

} 