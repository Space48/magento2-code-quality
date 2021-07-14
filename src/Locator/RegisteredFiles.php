<?php

declare(strict_types=1);

namespace Space48\CodeQuality\Locator;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Git\GitRepository;
use GrumPHP\Util\Paths;
use GrumPHP\Locator\ListedFiles;

// rewritten to catch 'not a git repo' error inside container where no '.git' folder is synced
class RegisteredFiles extends \GrumPHP\Locator\RegisteredFiles
{
    /**
     * @var GitRepository
     */
    private $repository;

    /**
     * @var Paths
     */
    private $paths;

    /**
     * @var ListedFiles
     */
    private $listedFiles;

    public function __construct(GitRepository $repository, Paths $paths, ListedFiles $listedFiles)
    {
        $this->repository = $repository;
        $this->paths = $paths;
        $this->listedFiles = $listedFiles;
    }

    public function locate(): FilesCollection
    {
        try {
            $allFiles = trim((string)$this->repository->run('ls-files', [$this->paths->getProjectDir()]));
        } catch (\Gitonomy\Git\Exception\ProcessException $e) {
            $allFiles = '';
        }

        return $this->listedFiles->locate($allFiles);
    }
}
