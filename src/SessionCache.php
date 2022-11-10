<?php

namespace WonderWp\Component\Cache;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCache implements CacheInterface
{
    /** @var SessionInterface */
    protected $session;

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $element = $this->session->get($key);

        //Return the stored element value
        return isset($element['value']) ? $element['value'] : $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $expiry = empty($ttl) ? 0 : date('U') + $ttl;
        $this->session->set($key, ['value' => $value, 'expiry' => $expiry]);
        return true;
    }

    public function delete($key)
    {
        $this->session->remove($key);
        return true;
    }

    public function clear()
    {
        $this->session->clear();
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
        $element = $this->session->get($key)
        if (empty($element)) {
            return false;
        }

        //Is the stored element still valid?
        if ($this->isExpired($key)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isExpired(string $key)
    {
        $element = $this->session->get($key);
        if (empty($element)) {
            return true;
        }

        $expiry = isset($element['expiry']) ? $element['expiry'] : null;
        if ($expiry === 0) {
            $expired = false;
        } else {
            $expired = $expiry < date('U');
        }
        return $expired;
    }


}
