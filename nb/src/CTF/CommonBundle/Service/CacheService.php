<?php

namespace CTF\CommonBundle\Service;

/**
 * \CTF\CommonBundle\Service\CacheService
 * 
 * This class provides an APC-specific cache service
 * for this app
 */
class CacheService {
    
    public function store($key, $var, $ttl = 3600) {
        return \apc_store($key, $var, $ttl);
    }
    
    public function add($key, $var, $ttl = 3600) {
        return \apc_add($key, $var, $ttl);
    }
    
    public function has($key) {
        $success = false;
        \apc_fetch($key, $success);
        
        return (true == $success);
    }
    
    public function get($key) {
        return \apc_fetch($key);
    }
    
    public function delete($key) {
        return \apc_delete($key);
    }
}
