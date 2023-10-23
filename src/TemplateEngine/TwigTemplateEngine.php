<?php

namespace DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigTemplateEngine implements TemplateEngineInterface
{
    public const KEYWORD_ALL_VALUES = 'all_values';

    public const KEY_TEMPLATE = 'template';
    public const DEFAULT_TEMPLATES = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => "{% for key,value in ' . self::KEYWORD_ALL_VALUES . ' %}\n{{ key }}: {{ value }}\n{% endfor %}",
        TemplateEngineInterface::FORMAT_HTML => "<table>\n<tr>\n<td>Key</td>\n<td>Value</td>\n</tr>\n{% for key,value in ' . self::KEYWORD_ALL_VALUES . ' %}\n<tr>\n<td>{{ key }}</td>\n<td>{{ value }}</td>\n</tr>\n{% endfor %}\n</table>",
    ];

    protected const TEMPLATE_LABELS = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => 'Template (Plain Text)',
        TemplateEngineInterface::FORMAT_HTML => 'Template (HTML)',
    ];

    /**
     * @param array<string,mixed> $config
     * @param array<string,string|ValueInterface> $data
     */
    public function render(array $config, array $data): string
    {
        /**
         * Extend $data array with all_values
         * This can be used within twig templates foreach
         * Example:
         * {% for key,value in all_values %}
         *   {{ key }}: {{ value }} <br>
         * {% endfor %}
         */
        if (array_key_exists(self::KEYWORD_ALL_VALUES, $data)) {
            throw new DigitalMarketingFrameworkException('variable "all_values" still exists');
        }
        $data[self::KEYWORD_ALL_VALUES] = $data;

        $template = $config[static::KEY_TEMPLATE];
        $loader = new ArrayLoader();
        $twig = new Environment($loader);
        $template = $twig->createTemplate($template);
        $result = $template->render($data);

        return $result;
    }

    public function getSchema(string $format): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setSkipHeader(true);

        if (!isset(static::DEFAULT_TEMPLATES[$format])) {
            throw new DigitalMarketingFrameworkException(sprintf('unknown template format "%s"', $format));
        }
        $templateSchema = new StringSchema(static::DEFAULT_TEMPLATES[$format]);
        $templateSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_TEXT);
        $templateSchema->getRenderingDefinition()->setLabel(static::TEMPLATE_LABELS[$format]);
        $schema->addProperty(static::KEY_TEMPLATE, $templateSchema);

        return $schema;
    }
}
