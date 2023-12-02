<?php

namespace kalanis\kw_cache_psr\Adapters;


use DateInterval;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use Psr\SimpleCache\CacheInterface;


/**
 * Class FilesAdapter
 * @package kalanis\kw_cache_psr\Adapters
 * Files adapter for PSR Cache Interface
 */
class FilesAdapter implements CacheInterface
{
    use TCheckKey;

    /** @var CompositeAdapter */
    protected $files = null;
    /** @var IFormat */
    protected $format = null;
    /** @var ArrayPath */
    protected $arr = null;
    /** @var string[] */
    protected $initialPath = null;

    /**
     * @param CompositeAdapter $files
     * @param IFormat $format
     * @param string[] $initialPath
     */
    public function __construct(CompositeAdapter $files, IFormat $format, array $initialPath = [])
    {
        $this->files = $files;
        $this->format = $format;
        $this->initialPath = $initialPath;
        $this->arr = new ArrayPath();
    }

    /**
     * @param string $key
     * @param mixed $default
     * @throws InvalidArgumentException
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        try {
            if ($this->has($key)) {
                return $this->format->unpack($this->files->readFile($this->fullKey($key)));
            }
            return $default;
        } catch (CacheException | FilesException | PathsException $ex) {
            return $default;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @throws InvalidArgumentException
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        try {
            return $this->files->saveFile($this->fullKey($key), strval($this->format->pack($value)));
        } catch (CacheException | FilesException | PathsException $ex) {
            return false;
        }
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @return bool
     */
    public function delete($key): bool
    {
        try {
            return $this->files->deleteFile($this->fullKey($key));
        } catch (FilesException | PathsException $ex) {
            return false;
        }
    }

    public function clear(): bool
    {
        try {
            $result = true;
            foreach ($this->files->readDir($this->initialPath) as $item) {
                /** @var Node $item */
                $result = $result && $this->files->deleteFile($item->getPath());
            }
            return $result;
        } catch (FilesException | PathsException $ex) {
            return false;
        }
    }

    /**
     * @param iterable<string|int, string> $keys
     * @param mixed $default
     * @throws InvalidArgumentException
     * @return iterable<string, mixed>
     */
    public function getMultiple($keys, $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * @param iterable<string, mixed> $values
     * @param null|int|DateInterval $ttl
     * @throws InvalidArgumentException
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    /**
     * @param iterable<string|int, string> $keys
     * @throws InvalidArgumentException
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        $result = true;
        foreach ($keys as $item) {
            $result = $result && $this->delete($item);
        }
        return $result;
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @return bool
     */
    public function has($key): bool
    {
        try {
            $useKey = $this->fullKey($key);
            return $this->files->exists($useKey) && $this->files->isFile($useKey);
        } catch (FilesException | PathsException $ex) {
            return false;
        }
    }

    /**
     * @param string $initialKey
     * @throws PathsException
     * @throws InvalidArgumentException
     * @return string[]
     */
    protected function fullKey(string $initialKey): array
    {
        return array_values($this->initialPath + $this->arr->setString($this->checkKey($initialKey))->getArray());
    }
}
