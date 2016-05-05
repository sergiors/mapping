<?php

namespace Sergiors\Mapping\Configuration\Metadata;

use Doctrine\Common\Cache\Cache;
use Sergiors\Mapping\Configuration\Metadata\Driver\MappingDriverInterface;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * @var MappingDriverInterface
     */
    private $mappingDriver;

    /**
     * @var Cache
     */
    private $cacheDriver;

    /**
     * @param MappingDriverInterface $mappingDriver
     * @param Cache|null             $cacheDriver
     */
    public function __construct(MappingDriverInterface $mappingDriver, Cache $cacheDriver = null)
    {
        $this->mappingDriver = $mappingDriver;
        $this->cacheDriver = $cacheDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataForClass($className)
    {
        if (null === $this->cacheDriver) {
            return $this->mappingDriver->loadMetadataForClass($className);
        }

        if ($this->cacheDriver->contains($className)) {
            return $this->cacheDriver->fetch($className);
        }

        $this->cacheDriver->save($className, $data = $this->mappingDriver->loadMetadataForClass($className));

        return $data;
    }
}
