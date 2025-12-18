<?php

namespace WonderWp\Component\Cache;

class FileCache implements CacheInterface
{
    /** @var string */
    private $cacheDir;

    /**
     * FileCache constructor.
     *
     * @param string $cacheDir Cache directory path.
     */
    public function __construct(string $cacheDir)
    {
        $this->cacheDir = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->ensureCacheDir();
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $this->validateKey($key);
        $filePath = $this->getCacheFilePath($key);

        if (!file_exists($filePath)) {
            return $default;
        }

        $data = $this->readCacheFile($filePath);
        if ($data === false) {
            return $default;
        }

        // Check if expired
        if ($this->isExpired($data)) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $ttl = null)
    {
        $this->validateKey($key);
        $filePath = $this->getCacheFilePath($key);

        $data = [
            'value' => $value,
            'ttl' => $ttl,
            'expires' => $ttl !== null ? time() + $ttl : null,
            'created' => time(),
        ];

        return $this->atomicWrite($filePath, serialize($data));
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->validateKey($key);
        $filePath = $this->getCacheFilePath($key);

        if (file_exists($filePath)) {
            return @unlink($filePath);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        if (!is_dir($this->cacheDir)) {
            return true;
        }

        $files = glob($this->cacheDir . '*');
        $success = true;

        foreach ($files as $file) {
            if (is_file($file)) {
                if (!@unlink($file)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function getMultiple($keys, $default = null)
    {
        $cached = [];
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $cached[$key] = $this->get($key, $default);
            }
        }
        return $cached;
    }

    /**
     * @inheritdoc
     */
    public function setMultiple($values, $ttl = null)
    {
        $success = true;
        if (!empty($values)) {
            foreach ($values as $key => $val) {
                $thisSuccess = $this->set($key, $val, $ttl);
                if (!$thisSuccess) {
                    $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * @inheritdoc
     */
    public function deleteMultiple($keys)
    {
        $success = true;
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $thisSuccess = $this->delete($key);
                if (!$thisSuccess) {
                    $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        $this->validateKey($key);
        $filePath = $this->getCacheFilePath($key);

        if (!file_exists($filePath)) {
            return false;
        }

        $data = $this->readCacheFile($filePath);
        if ($data === false) {
            return false;
        }

        return !$this->isExpired($data);
    }

    /**
     * Get the full file path for a cache key
     *
     * @param string $key
     * @return string
     */
    private function getCacheFilePath($key)
    {
        // Sanitize key to be filesystem-safe
        $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        return $this->cacheDir . md5($key) . '_' . $safeKey . '.cache';
    }

    /**
     * Check if cache data is expired
     *
     * @param array $data
     * @return bool
     */
    private function isExpired($data)
    {
        if (!isset($data['expires']) || $data['expires'] === null) {
            return false; // No expiration
        }

        return time() > $data['expires'];
    }

    /**
     * Read and unserialize cache file
     *
     * @param string $filePath
     * @return array|false
     */
    private function readCacheFile($filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        $content = @file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        $data = @unserialize($content);
        if ($data === false || !is_array($data)) {
            return false;
        }

        return $data;
    }

    /**
     * Write cache file atomically
     *
     * @param string $filePath
     * @param string $content
     * @return bool
     */
    private function atomicWrite($filePath, $content)
    {
        $tempFile = $filePath . '.tmp';
        $written = @file_put_contents($tempFile, $content, LOCK_EX);

        if ($written === false) {
            return false;
        }

        // Atomic rename
        if (@rename($tempFile, $filePath) === false) {
            @unlink($tempFile);
            return false;
        }

        return true;
    }

    /**
     * Ensure cache directory exists
     */
    private function ensureCacheDir()
    {
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Validate cache key
     *
     * @param string $key
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (!is_string($key) || empty($key)) {
            throw new InvalidArgumentException('Cache key must be a non-empty string');
        }
    }
}






