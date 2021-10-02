<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\Reader;
use Ergebnis\Classy\Construct;
use Ergebnis\Classy\Constructs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LintAnnotationCommand extends Command
{
    protected static $defaultName = 'app:lint-annotation';

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(string $projectDir, Reader $reader)
    {
        $this->projectDir = $projectDir;
        $this->reader = $reader;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('lit annotation in src directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $errors = $this->validateClasses($this->getAllClasses());

        if (empty($errors)) {
            $io->success('Everything is fine');

            return 0;
        }

        $io->error(sprintf('Found %d issues.', \count($errors)));

        foreach ($errors as $error) {
            $io->error($error->getMessage());
        }

        return 1;
    }

    /**
     * @param string[] $classes
     * @psalm-param list<class-string> $classes
     * @return AnnotationException[]
     * @psalm-return list<AnnotationException>
     */
    private function validateClasses(array $classes): array
    {
        $exceptions = [];

        foreach ($classes as $class) {
            try {
                $this->validateClass($class);
            } catch (AnnotationException $e) {
                $exceptions[] = $e;
            }
        }

        return $exceptions;
    }

    /**
     * @psalm-param class-string $class
     */
    private function validateClass(string $class): void
    {
        $reflectionClass = new \ReflectionClass($class);
        $this->reader->getClassAnnotations($reflectionClass);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $this->reader->getMethodAnnotations($reflectionMethod);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $this->reader->getPropertyAnnotations($reflectionProperty);
        }
    }

    /**
     * @return string[]
     * @psalm-return list<class-string>
     */
    private function getAllClasses(): array
    {
        $constructs = Constructs::fromDirectory($this->projectDir . '/src');

        /** @psalm-var list<class-string> */
        return array_map(static fn (Construct $construct): string => $construct->name(), $constructs);
    }
}
