<?php

namespace DigitalMarketingFramework\TemplateEngineTwig\TemplateEngine;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\TemplateEngine\TemplateEngineInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class TwigTemplateEngine implements TemplateEngineInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public const KEYWORD_ALL_VALUES = 'all_values';

    public const KEY_TEMPLATE = 'template';

    public const DEFAULT_TEMPLATES = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => '{% for key,value in ' . self::KEYWORD_ALL_VALUES . " %}\n{{ key }}: {{ value }}\n{% endfor %}",
        TemplateEngineInterface::FORMAT_HTML => "<table>\n<tr>\n<td>Key</td>\n<td>Value</td>\n</tr>\n{% for key,value in " . self::KEYWORD_ALL_VALUES . " %}\n<tr>\n<td>{{ key }}</td>\n<td>{{ value }}</td>\n</tr>\n{% endfor %}\n</table>",
    ];

    protected const TEMPLATE_LABELS = [
        TemplateEngineInterface::FORMAT_PLAIN_TEXT => 'Template (Plain Text)',
        TemplateEngineInterface::FORMAT_HTML => 'Template (HTML)',
    ];

    public const KEY_TEMPLATE_NAME = 'templateName';

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
            throw new DigitalMarketingFrameworkException('variable "' . self::KEYWORD_ALL_VALUES . '" already exists');
        }

        $data[self::KEYWORD_ALL_VALUES] = $data;
        $templateService = $this->registry->getTemplateService();

        $template = $config[static::KEY_TEMPLATE] ?? '';
        $templateNames = GeneralUtility::castValueToArray($config[static::KEY_TEMPLATE_NAME] ?? '');
        foreach ($templateNames as $templateName) {
            $possibleTemplate = $templateService->getTemplate($templateName);
            if ($possibleTemplate !== null) {
                $template = $possibleTemplate;
                break;
            }
        }

        $templateFolders = $templateService->getPartialFolderPaths();

        $loader = $templateFolders === []
            ? new ArrayLoader()
            : new FilesystemLoader($templateFolders);
        $twig = new Environment($loader);

        try {
            $template = $twig->createTemplate($template);

            return $template->render($data);
        } catch (LoaderError|SyntaxError $e) {
            $this->logger->error($e->getMessage());
        }

        return '';
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
