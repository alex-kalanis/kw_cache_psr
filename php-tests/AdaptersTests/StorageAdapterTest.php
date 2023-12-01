<?php

namespace AdaptersTests;


use kalanis\kw_cache\Format\Serialized;
use kalanis\kw_cache_psr\Adapters\StorageAdapter;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use Psr\SimpleCache\CacheInterface;
use Traversable;


class StorageAdapterTest extends AAdaptersTest
{
    protected function getCacheInterfacePass(): CacheInterface
    {
        return new StorageAdapter(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()), new Serialized());
    }

    protected function getCacheInterfaceFail(): CacheInterface
    {
        return new StorageAdapter(new XFailStorage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()), new Serialized());
    }

    protected function getCacheInterfaceFailExists(): CacheInterface
    {
        return new StorageAdapter(new XFailStorageExists(new Storage\Key\DefaultKey(), new Storage\Target\Memory()), new Serialized());
    }

    protected function getCacheInterfaceFailWrite(): CacheInterface
    {
        return new StorageAdapter(new XFailStorageWrite(new Storage\Key\DefaultKey(), new Storage\Target\Memory()), new Serialized());
    }

    protected function getCacheInterfaceFailUnpack(): CacheInterface
    {
        return new StorageAdapter(new Storage\Storage(new Storage\Key\DefaultKey(), new Storage\Target\Memory()), new XSerializedFail());
    }
}


class XFailStorage extends Storage\Storage
{
    public function remove(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }

    public function lookup(string $mask): Traversable
    {
        throw new StorageException('mock');
    }
}


class XFailStorageExists extends XFailStorage
{
    public function exists(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }
}


class XFailStorageWrite extends XFailStorage
{
    public function write(string $sharedKey, $data, ?int $timeout = null): bool
    {
        throw new StorageException('mock');
    }
}
