<?php

namespace DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    protected array $filters = [];

    protected array $functions = [];

    public function addFilter(string $name, callable $fnc): void
    {
        $this->filters[$name] = new TwigFilter($name, $fnc);
    }

    public function removeFilter(string $name): void
    {
        unset($this->filters[$name]);
    }

    public function getFilters(): array
    {
        return array_values($this->filters);
    }

    public function addFunction(string $name, callable $fnc): void
    {
        $this->functions[$name] = new TwigFunction($name, $fnc);
    }

    public function removeFunction(string $name): void
    {
        unset($this->filters[$name]);
    }

    public function getFunctions(): array
    {
        return array_values($this->functions);
    }
}
