<?php declare(strict_types=1);

namespace MarekNocon\ComposerVendorStability;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PrePoolCreateEvent;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use MarekNocon\ComposerVendorStability\Filter\PackageStabilityFilter;

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
        $packageFilter = new PackageStabilityFilter();
        foreach ($event->getPackages() as $package) {
            if ($packageFilter->matchesStabilityConstraints(
                $package,
                $event->getStabilityFlags(),
                $this->stabilityConfig,
                $this->minimumStability
            )
            ) {
                $matchingPackages[] = $package;
            }
        }
        $event->setPackages($matchingPackages);
    }
}
