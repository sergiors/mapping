<?php

namespace Sergiors\Mapping\Configuration\Metadata;

/**
 * @author Sérgio Rafael Siqueira <sergio@inbep.com.br>
 */
interface ClassMetadataFactoryInterface
{
    /**
     * @param string $className
     *
     * @return array
     */
    public function getMetadataForClass($className);
}
