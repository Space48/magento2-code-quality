<?php

namespace Space48\CodeQuality\Utils;

use PHPMD\AbstractNode;

class MagentoClassTypeResolver
{
    public static function isPlugin(AbstractNode $node)
    {
        foreach (self::locateFiles($node, 'di.xml') as $path) {
            $domDocument = new \DomDocument('1.0', 'UTF-8');
            $domDocument->loadXML(\file_get_contents($path . 'di.xml'));
            $domXPath = new \DOMXPath($domDocument);

            /** @var \DOMElement $nodeMatches [] */
            $nodeMatches = $domXPath->query(
                \sprintf(
                    '//plugin[@type="%s"]',
                $node->getNamespaceName() . '\\' . $node->getParentName()
                )
            );

            if ($nodeMatches->length) {
                return true;
            }
        }
    }

    public static function isPluginMethod(AbstractNode $node)
    {
        return preg_match('/^(before|after|around)[A-Z].*/', $node->getName());
    }

    private function locateFiles(AbstractNode $node, $filename)
    {
        $namespace = explode('\\', $node->getNamespaceName());
        $path = strstr($node->getFileName(), $namespace[2], true) . 'etc';

        return $this->searchFiles($path, $filename);
    }

    private function searchFiles($path, $fileName)
    {
        $configFiles = [];

        foreach (scandir($path) as $directory) {
            $filePath = $path . DIRECTORY_SEPARATOR . $directory;
            if ($directory == $fileName) {
                return $filePath;
            } elseif (is_dir($filePath)) {
                $configFiles[] = $this->searchFiles($filePath);
            }
        }

        return $configFiles;
    }

}
