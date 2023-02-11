<?php declare(strict_types=1);

namespace MarekNocon\Tests\ComposerStabilityPatterns\Filter;

use Composer\Package\BasePackage;
use Composer\Package\Package;
use MarekNocon\ComposerStabilityPatterns\Filter\PackageStabilityFilter;
use PHPUnit\Framework\TestCase;

class PackageStabilityFilterTest extends TestCase
{
    /**
     * @param array<string, string> $stabilityConfig
     * @param array<string, int> $stabilityFlags
     *
     * @dataProvider providerFor_testPackageMinimumStability
     * @dataProvider providerFor_testPatternMinimumStability
     * @dataProvider providerFor_testGlobalMinimumStability
     */
    public function testMatchesStabilityConstraint(
        BasePackage $package,
        array $stabilityFlags,
        array $stabilityConfig,
        string $minimumStability,
        bool $expectedResult
    ): void {
        $packageFilter = new PackageStabilityFilter();
        self::assertEquals(
            $expectedResult,
            $packageFilter->matchesStabilityConstraints($package, $stabilityFlags, $stabilityConfig, $minimumStability)
        );
    }

    public static function providerFor_testPackageMinimumStability(): array
    {
        $stabilityFlags = [
            'mareknocon/rc-package' => BasePackage::STABILITY_RC,
            'mareknocon/beta-package' => BasePackage::STABILITY_BETA,
            'mareknocon/alpha-package' => BasePackage::STABILITY_ALPHA,
            'mareknocon/dev-package' => BasePackage::STABILITY_DEV,
        ];

        return [
            [self::createPackage('mareknocon/rc-package', '1.0.2'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/rc-package', '1.0.2-rc1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/rc-package', '1.0.2@rc'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/rc-package', '1.0.2-beta'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/rc-package', '1.0.2@beta'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/rc-package', '1.0.2-alpha1'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/rc-package', '1.0.2@alpha'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/rc-package', '1.0.x-dev'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/rc-package', 'dev-main'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/beta-package', '1.0.2'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/beta-package', '1.0.2-rc1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/beta-package', '1.0.2@rc'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/beta-package', '1.0.2-beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/beta-package', '1.0.2@beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/beta-package', '1.0.2-alpha1'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/beta-package', '1.0.2@alpha'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/beta-package', '1.0.x-dev'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/beta-package', 'dev-main'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/alpha-package', '1.0.2'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2-rc1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2@rc'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2-beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2@beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2-alpha1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.2@alpha'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/alpha-package', '1.0.x-dev'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/alpha-package', 'dev-main'), $stabilityFlags, ['*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/dev-package', '1.0.2'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2-rc1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2@rc'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2-beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2@beta'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2-alpha1'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.2@alpha'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', '1.0.x-dev'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/dev-package', 'dev-main'), $stabilityFlags, ['*' => 'stable'], 'stable', true],
        ];
    }

    public static function providerFor_testPatternMinimumStability(): array
    {
        return [
            [self::createPackage('mareknocon/package', '1.0.0'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-alpha'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-alpha'), [], ['mareknocon/package' => 'alpha'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], ['mareknocon/package' => 'alpha'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/package' => 'alpha'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], ['mareknocon/package' => 'beta'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], ['mareknocon/package' => 'beta'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc'), [], ['mareknocon/package' => 'beta'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], ['mareknocon/package' => 'beta'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta'), [], ['mareknocon/package' => 'beta'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], ['mareknocon/package' => 'beta'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha'), [], ['mareknocon/package' => 'beta'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], ['mareknocon/package' => 'beta'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/package' => 'beta'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], ['mareknocon/package' => 'rc'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], ['mareknocon/package' => 'rc'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc'), [], ['mareknocon/package' => 'rc'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-beta'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/package' => 'rc'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], ['mareknocon/package' => 'stable'], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-rc'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-beta'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/package' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['*' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['*/*' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['mareknocon/*' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['*/package' => 'dev'], 'stable', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['symfony/*' => 'stable'], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], ['symfony/package' => 'stable'], 'stable', false],
        ];
    }

    public static function providerFor_testGlobalMinimumStability(): array
    {
        return [
            [self::createPackage('mareknocon/package', '1.0.0'), [], [], 'stable', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-rc1'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-beta1'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha1'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], [], 'stable', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], [], 'rc', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], [], 'rc', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc1'), [], [], 'rc', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', '1.0.0-beta1'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha1'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], [], 'rc', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], [], 'beta', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], [], 'beta', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc1'), [], [], 'beta', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], [], 'beta', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta1'), [], [], 'beta', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], [], 'beta', false],
            [self::createPackage('mareknocon/package', '1.0.0-alpha1'), [], [], 'beta', false],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], [], 'beta', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], [], 'beta', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc1'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta1'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.0-alpha1'), [], [], 'alpha', true],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], [], 'alpha', false],
            [self::createPackage('mareknocon/package', 'dev-main'), [], [], 'alpha', false],
            [self::createPackage('mareknocon/package', '1.0.0'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0@rc'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0-rc1'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0@beta'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0-beta1'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0@alpha'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.0-alpha1'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', '1.0.x-dev'), [], [], 'dev', true],
            [self::createPackage('mareknocon/package', 'dev-main'), [], [], 'dev', true],
        ];
    }

    public static function createPackage(string $name, $version): BasePackage
    {
        return new Package($name, $version, '');
    }
}
