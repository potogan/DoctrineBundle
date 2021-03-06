<?php

namespace Potogan\DoctrineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PotoganDoctrineBundle extends Bundle
{
    public function boot()
    {
        $config = $this->container->get('doctrine.orm.entity_manager')->getConfiguration();

        $config->addCustomHydrationMode('entities', 'Potogan\\DoctrineBundle\\Hydrators\\MultipleEntitiesHydrator');
        $config->addCustomStringFunction('IF', 'Potogan\\DoctrineBundle\\Query\\Functions\\Mysql\\MysqlIf');
        $config->addCustomStringFunction('FIND_IN_SET', 'Potogan\\DoctrineBundle\\Query\\Functions\\Mysql\\FindInSet');
        $config->addCustomStringFunction('GROUP_BIT_OR', 'Potogan\\DoctrineBundle\\Query\\Functions\\Mysql\\BitOrGrouping');
        $config->addCustomStringFunction('GROUP_BIT_AND', 'Potogan\\DoctrineBundle\\Query\\Functions\\Mysql\\BitAndGrouping');
    }
}
