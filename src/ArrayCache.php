<?php

namespace WonderWp\Component\Cache;

class ArrayCache implements CacheInterface
{
    /** @var array */
    protected $storage = [];

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        //Return the stored element value
        return isset($this->storage[$key]['value']) ? $this->storage[$key]['value'] : $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isExpired(string $key)
    {
        $expiry = isset($this->storage[$key]['expiry']) ? $this->storage[$key]['expiry'] : null;
        if ($expiry === 0) {
            $expired = false;
        } else {
            $expired = $expiry < date('U');
        }
        return $expired;
    }

    public function set($key, $value, $ttl = null)
    {
        $expiry              = empty($ttl) ? 0 : date('U') + $ttl;
        $this->storage[$key] = ['value' => $value, 'expiry' => $expiry];
        return true;
    }

    public function delete($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
        }
        return true;
    }

    public function clear()
    {
        $this->storage = [];
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $values = [];
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $values[$key] = $this->get($key, $default);
            }
        }
        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        $success = true;
        if (!empty($values)) {
            foreach ($values as $key => $val) {
                $thisSuccess = $this->set($key, $val, $ttl);
                if (!$thisSuccess) {
                    $success = $thisSuccess;
                }
            }
        }
        return $success;
    }

    public function deleteMultiple($keys)
    {
        $success = true;
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $thisSuccess = $this->delete($key);
                if (!$thisSuccess) {
                    $success = $thisSuccess;
                }
            }
        }
        return $success;
    }

    public function has($key)
    {
        //Do we have a stored element for this key?
        $element = isset($this->storage[$key]) ? $this->storage[$key] : null;
        if (empty($element)) {
            return false;
        }

        //Is the stored element still valid?
        if ($this->isExpired($key)) {
            return false;
        }

        return true;
    }

}
