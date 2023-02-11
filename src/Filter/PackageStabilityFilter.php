<?php declare(strict_types=1);

namespace MarekNocon\ComposerVendorStability\Filter;

use Composer\Package\BasePackage;
use Composer\Package\Version\VersionParser;
use Composer\Pcre\Preg;

class PackageStabilityFilter
{
    /**
     * @param array<string, int> $stabilityFlags
     * @param array<string, string> $stabilityConfig
     */
    public function matchesStabilityConstraints(
        BasePackage $package,
        array $stabilityFlags,
        array $stabilityConfig,
        string $minimumStability
    ): bool {
        $packageName = $package->getName();
        $packageStabilityLevel = $this->getStabilityLevel($package->getStability());

        // stability flags (specified per individual package) have the highest priority
        if (array_key_exists($packageName, $stabilityFlags)) {
            return $packageStabilityLevel <= $stabilityFlags[$packageName];
        }

        // then we match against the vendor stability level
        foreach ($stabilityConfig as $pattern => $stability) {
            if (Preg::isMatch(BasePackage::packageNameToRegexp($pattern), $packageName)) {
                $vendorStabilityLevel = $this->getStabilityLevel($stability);

                return $this->getStabilityLevel($package->getStability()) <= $vendorStabilityLevel;
            }
        }

        // at the end we take the original stability level into account
        return $packageStabilityLevel <= $this->getStabilityLevel($minimumStability);
    }

    private function getStabilityLevel(string $stability): int
    {
        $stability = VersionParser::normalizeStability($stability);

        return BasePackage::$stabilities[$stability];
    }
}
