<?php

namespace DigitalMarketingFramework\TemplateEngineTwig;

use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine\TwigTemplateEngine;

class TemplateEngineTwigInitialization extends Initialization
{
    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('template-engine-twig', '1.0.0', $packageAlias);
    }

    public function initServices(string $domain, RegistryInterface $registry): void
    {
        $template = $registry->createObject(TwigTemplateEngine::class, [$registry]);
        $registry->setTemplateEngine($template);
    }
}
