<?php

namespace AdaptersTests;


use kalanis\kw_cache\Format\Serialized;
use kalanis\kw_cache_psr\Adapters\SimpleCache\FilesAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;
use Psr\SimpleCache\CacheInterface;
use Traversable;


class FilesAdapterTest extends AAdaptersTest
{
    protected function getCacheInterfacePass(): CacheInterface
    {
        return new FilesAdapter((new Factory())->getClass(new Storage\Storage(new Storage\Key\DefaultKey(), $this->getMemory())), new Serialized());
    }

    protected function getCacheInterfaceFail(): CacheInterface
    {
        return new FilesAdapter((new Factory())->getClass(new XFailFiles(new Storage\Key\DefaultKey(), $this->getMemory())), new Serialized());
    }

    protected function getCacheInterfaceFailExists(): CacheInterface
    {
        return new FilesAdapter((new Factory())->getClass(new XFailFilesExists(new Storage\Key\DefaultKey(), $this->getMemory())), new Serialized());
    }

    protected function getCacheInterfaceFailWrite(): CacheInterface
    {
        return new FilesAdapter((new Factory())->getClass(new XFailFilesWrite(new Storage\Key\DefaultKey(), $this->getMemory())), new Serialized());
    }

    protected function getCacheInterfaceFailUnpack(): CacheInterface
    {
        return new FilesAdapter((new Factory())->getClass(new Storage\Storage(new Storage\Key\DefaultKey(), $this->getMemory())), new XSerializedFail());
    }

    protected function getMemory(): Storage\Target\Memory
    {
        $memory = new Storage\Target\Memory();
        $memory->save('', IProcessNodes::STORAGE_NODE_KEY); // root node
        return $memory;
    }
}


class XFailFiles extends Storage\Storage
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


class XFailFilesExists extends XFailFiles
{
    public function exists(string $sharedKey): bool
    {
        throw new StorageException('mock');
    }
}


class XFailFilesWrite extends XFailFiles
{
    public function write(string $sharedKey, $data, ?int $timeout = null): bool
    {
        throw new StorageException('mock');
    }
}
