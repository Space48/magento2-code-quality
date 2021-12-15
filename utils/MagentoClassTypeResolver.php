<?php

namespace Space48\CodeQuality\Utils;

use PHPMD\AbstractNode;

class MagentoClassTypeResolver
{
    private $types = [];

    private $typeConfig = [
        'plugin' => [
            'xmlNode' => 'plugin',
            'xmlAttribute' => 'type',
            'configFile' => 'di.xml'
        ],
        'observer' => [
            'xmlNode' => 'observer',
            'xmlAttribute' => 'instance',
            'configFile' => 'events.xml'
        ]
    ];

    /**
     * Returns type of the Magento class - 'plugin', 'observer', etc
     *
     * @param AbstractNode $node
     * @return false|string
     */
    public function resolveType(AbstractNode $node)
    {
        if (!$this->types[$this->getClassName($node)]) {
            foreach ($this->typeConfig as $typeName => $type) {
                foreach (self::locateFiles($node, $type['configFile']) as $filePath) {
                    $domDocument = new \DomDocument('1.0', 'UTF-8');
                    $domDocument->loadXML(\file_get_contents($filePath));
                    $domXPath = new \DOMXPath($domDocument);

                    /** @var \DOMElement $nodeMatches [] */
                    $nodeMatches = $domXPath->query(
                        \sprintf(
                            '//%s[@%s="%s"]',
                            $type['xmlNode'],
                            $type['xmlAttribute'],
                            $this->getClassName($node)
                        )
                    );

                    if ($nodeMatches->length) {
                        $this->types[$this->getClassName($node)] = $typeName;
                        break 2;
                    }
                }
            }
        }

        return $this->types[$this->getClassName($node)] ?? false;
    }

    /**
     * @param AbstractNode $node
     * @return string
     */
    private function getClassName(AbstractNode $node)
    {
        return $node->getNamespaceName() . '\\' . $node->getParentName();
    }

    /**
     * @param AbstractNode $node
     * @param string $filename
     * @return array
     */
    private function locateFiles(AbstractNode $node, string $filename): array
    {
        $namespace = explode('\\', $node->getNamespaceName());
        // [2] will be the name of the folder just after Module folder in standard Magento module structure
        if (empty($namespace[2])) {
            return [];
        }

        $path = strstr($node->getFileName(), $namespace[2], true) . 'etc';

        return array_filter($this->searchFiles($path, $filename));
    }

    /**
     * @param string $path
     * @param string $fileName
     * @return array
     */
    private function searchFiles(string $path, string $fileName): array
    {
        $configFiles = [];

        foreach (scandir($path) as $directory) {
            if ($directory == '.' || $directory == '..') {
                continue;
            }

            $filePath = $path . DIRECTORY_SEPARATOR . $directory;
            if ($directory == $fileName) {
                $configFiles[] = $filePath;
            } elseif (is_dir($filePath)) {
                $configFiles = array_merge($configFiles, $this->searchFiles($filePath, $fileName));
            }
        }

        return $configFiles;
    }

}
