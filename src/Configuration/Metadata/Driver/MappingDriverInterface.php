<?php

namespace Sergiors\Mapping\Configuration\Metadata\Driver;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
interface MappingDriverInterface
{
    /**
     * @param string $className
     *
     * @return array
     */
    public function loadMetadataForClass($className);
}