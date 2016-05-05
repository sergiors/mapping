<?php

namespace Sergiors\Mapping\Configuration\Metadata;

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
     * @param MappingDriverInterface $mappingDriver
     */
    public function __construct(MappingDriverInterface $mappingDriver)
    {
        $this->mappingDriver = $mappingDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataForClass($className)
    {
        return $this->mappingDriver->loadMetadataForClass($className);
    }
}