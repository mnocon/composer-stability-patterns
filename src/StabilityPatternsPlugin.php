<?php declare(strict_types=1);

namespace MarekNocon\ComposerStabilityPatterns;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PrePoolCreateEvent;
use MarekNocon\ComposerStabilityPatterns\Filter\PackageStabilityFilter;

class StabilityPatternsPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var array<string, string> */
    private array $stabilityConfig;

    private string $minimumStability;

    public function activate(Composer $composer, IOInterface $io): void
    {
        if ($io->isVerbose()) {
            $io->debug('Activating StabilityPatterns plugin');
        }
        $basePackage = $composer->getPackage();
        $this->stabilityConfig = $basePackage->getExtra()['minimum-stability'] ?? [];
        $this->minimumStability = $basePackage->getMinimumStability();
        $basePackage->setMinimumStability('dev');
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        if ($io->isVerbose()) {
            $io->debug('Deactivating StabilityPatterns plugin');
        }
        $composer->getPackage()->setMinimumStability($this->minimumStability);
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
            PluginEvents::PRE_POOL_CREATE => 'filterPackagePool',
        ];
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
