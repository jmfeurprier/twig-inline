<?php

namespace Jmf\Twig\Extension\Inline;

use Jmf\Twig\Extension\Inline\Exception\InlineException;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InlineExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    private readonly string $basePath;

    public function __construct(
        string $basePath,
        private readonly string $functionPrefix = self::PREFIX_DEFAULT,
    ) {
        $this->basePath = realpath($basePath) . '/';
    }

    #[Override]
    public function getFunctions(): iterable
    {
        return [
            new TwigFunction(
                "{$this->functionPrefix}inline",
                $this->inline(...),
                ['is_safe' => ['all']]
            ),
        ];
    }

    /**
     * @throws InlineException
     */
    public function inline(string $path): string
    {
        $absolutePath = $this->getAbsolutePath($path);

        $this->validatePath($absolutePath);

        return $this->getFileContent($absolutePath);
    }

    /**
     * @throws InlineException
     */
    private function getAbsolutePath(string $relativePath): string
    {
        $absolutePath = realpath($this->basePath . $relativePath);

        if (false === $absolutePath) {
            throw new InlineException("Failed to resolve absolute path from {$relativePath}");
        }

        return $absolutePath;
    }

    /**
     * @throws InlineException
     */
    private function validatePath(string $path): void
    {
        // Ensure provided path is not outside of base path (ex: "../../something.php").
        if (!str_starts_with($path, $this->basePath)) {
            throw new InlineException('Cannot inline content outside of base path.');
        }

        if (!file_exists($path)) {
            throw new InlineException('File to inline not found.');
        }

        if (!is_file($path)) {
            throw new InlineException('File to inline is not a file.');
        }
    }

    /**
     * @throws InlineException
     */
    private function getFileContent(string $path): string
    {
        $content = file_get_contents($path);

        if (false === $content) {
            throw new InlineException('Failed to retrieve content of inline file.');
        }

        return $content;
    }
}
