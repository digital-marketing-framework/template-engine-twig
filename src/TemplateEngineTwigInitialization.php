<?php

namespace DigitalMarketingFramework\TemplateEngineTwig;

use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine\TwigTemplateEngine;

class TemplateEngineTwigInitialization extends Initialization
{
    protected const PLUGINS = [];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('template-engine-twig', '1.0.0', $packageAlias);
    }

    public function initServices(string $domain, RegistryInterface $registry): void
    {
        /** @var TwigTemplateEngine */
        $template = $registry->createObject(TwigTemplateEngine::class);
        $registry->setTemplateEngine($template);
    }
}
