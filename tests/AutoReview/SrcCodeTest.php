<?php

declare(strict_types=1);

namespace App\Tests\AutoReview;

use App\Tests\Functional\SmokeTest;
use Ergebnis\Classy;
use Ergebnis\Test\Util\Helper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
class SrcCodeTest extends TestCase
{
    use Helper;

    public function testSrcSecurityVoterClassesHaveUnitTests(): void
    {
        $this->assertClassesImplementsInterfaceHaveTests(
            __DIR__ . '/../../src',
            'App\\',
            'App\\Tests\\Unit\\',
            VoterInterface::class
        );
    }

    public function testSrcConstraintValidatorClassesHaveUnitTests(): void
    {
        $this->assertClassesImplementsInterfaceHaveTests(
            __DIR__ . '/../../src',
            'App\\',
            'App\\Tests\\Unit\\',
            ConstraintValidatorInterface::class
        );
    }

    public function testTestsClassesHasSrcClasses(): void
    {
        $this->assertTestClassesHasSrcClasses(
            __DIR__ . '/..',
            'App\\',
            [
                'App\\Tests\\Unit\\',
                'App\\Tests\\Integration\\',
                'App\\Tests\\Functional\\',
            ],
            [
                self::class,
                SmokeTest::class,
            ]
        );
    }

    /**
     * @psalm-param list<class-string> $excludeClassyNames
     * @param string[] $excludeClassyNames
     * @psalm-param class-string $implementsInterface
     */
    public static function assertClassesImplementsInterfaceHaveTests(
        string $directory,
        string $namespace,
        string $testNamespace,
        string $implementsInterface,
        array $excludeClassyNames = []
    ): void {
        $classyNames = array_filter(self::getClassesFromDirectory($directory), static function (string $className) use ($implementsInterface): bool {
            $reflection = new \ReflectionClass($className);

            if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isTrait()) {
                return false;
            }

            return !$reflection->implementsInterface($implementsInterface);
        });

        self::assertClassesHaveTests(
            $directory,
            $namespace,
            $testNamespace,
            array_merge($classyNames, $excludeClassyNames)
        );
    }

    /**
     * @param string[] $testNamespacesPrefixes
     * @param string[] $excludeTestClassyNames
     */
    private static function assertTestClassesHasSrcClasses(
        string $testsDirectory,
        string $srcNamespace,
        array $testNamespacesPrefixes,
        array $excludeTestClassyNames = []
    ): void {
        $testClassNames = array_filter(self::getClassesFromDirectory($testsDirectory), static function (string $className) use ($excludeTestClassyNames): bool {
            if (\in_array($className, $excludeTestClassyNames, true)) {
                return false;
            }

            if (!str_ends_with($className, 'Test')) {
                return false;
            }

            $reflection = new \ReflectionClass($className);

            if ($reflection->isAbstract() || $reflection->isInterface() || $reflection->isTrait()) {
                return false;
            }

            if ($reflection->isSubclassOf(TestCase::class) && $reflection->isInstantiable()) {
                return true;
            }

            return false;
        });

        $expectedSourceClassNames = array_map(static function (string $testClassName) use ($srcNamespace, $testNamespacesPrefixes): string {
            Assert::endsWith($testClassName, 'Test');

            return substr(str_replace(
                $testNamespacesPrefixes,
                $srcNamespace,
                $testClassName
            ), 0, -4);
        }, $testClassNames);

        $notExistsSourceClasses = array_filter($expectedSourceClassNames, static fn (string $className): bool => !class_exists($className));

        $testClassNamesWithNotExistSourceClass = array_intersect_key($testClassNames, $notExistsSourceClasses);

        $generateClassesNames = static function (string $className): string {
            return sprintf(
                ' - %s',
                $className
            );
        };

        self::assertEmpty($testClassNamesWithNotExistSourceClass, sprintf(
            "Failed asserting that the test classes\n\n%s\n\n not has class is src. Expected src classes\n\n%s\n\n but could not find them.",
            implode("\n", array_map($generateClassesNames, $testClassNamesWithNotExistSourceClass)),
            implode("\n", array_map($generateClassesNames, $notExistsSourceClasses))
        ));
    }

    /**
     * @return string[]
     * @psalm-return class-string[]
     */
    private static function getClassesFromDirectory(string $directory): array
    {
        $constructs = Classy\Constructs::fromDirectory($directory);

        /** @psalm-var class-string[] */
        return array_map('strval', $constructs);
    }
}
