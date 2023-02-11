<?php declare(strict_types=1);

namespace MarekNocon\ComposerVendorStability;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\Version\VersionParser;
use Composer\Pcre\Preg;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class VendorStabilityPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var array<string, string> */
    private array $stabilityConfig;

    private string $minimumStability;

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::PRE_UPDATE_CMD => 'getConfig',
            ScriptEvents::PRE_INSTALL_CMD => 'getConfig',
            PluginEvents::PRE_POOL_CREATE => 'filterPackagePool',
        ];
    }

    public function getConfig(Event $event): void
    {
        $this->stabilityConfig = $event->getComposer()->getPackage()->getExtra()['minimum-stability'] ?? [];
        $this->minimumStability = $event->getComposer()->getPackage()->getMinimumStability();
        $event->getComposer()->getPackage()->setMinimumStability('dev');
    }

    public function filterPackagePool(PrePoolCreateEvent $event): void
    {
        $matchingPackages = [];
        foreach ($event->getPackages() as $package) {
            if ($this->matchesStabilityConstraints($package, $this->stabilityConfig, $event->getStabilityFlags())) {
                $matchingPackages[] = $package;
            }
        }
        $event->setPackages($matchingPackages);
    }

    /**
     * @param array<string, string> $stabilityConfig
     * @param array<string, int> $stabilityFlags
     */
    private function matchesStabilityConstraints(BasePackage $package, array $stabilityConfig, array $stabilityFlags): bool
    {
        $packageName = $package->getName();
        $packageStabilityLevel = $this->getStabilityLevel($package->getStability());

        // stability flags (specified per indiviudal package) have the highest priority
        if (in_array($packageName, $stabilityFlags, true)) {
            return $packageStabilityLevel <= $stabilityFlags[$packageName];
        }

        // then we match against the vendor stability level
        foreach ($stabilityConfig as $pattern => $stability) {
            if (Preg::isMatch('{' . $pattern . '}', $packageName)) {
                $vendorStabilityLevel = $this->getStabilityLevel($stability);

                return $this->getStabilityLevel($package->getStability()) <= $vendorStabilityLevel;
            }
        }

        // at the end we take the original stability level into account
        return $packageStabilityLevel <= $this->getStabilityLevel($this->minimumStability);
    }

    private function getStabilityLevel(string $stability): int
    {
        $stability = VersionParser::normalizeStability($stability);

        return BasePackage::$stabilities[$stability];
    }
}
