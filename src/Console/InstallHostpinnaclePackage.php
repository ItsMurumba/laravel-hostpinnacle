<?php

namespace Itsmurumba\Hostpinnacle\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Artisan command to publish the Hostpinnacle config file.
 */
class InstallHostpinnaclePackage extends Command
{
    /** @var string */
    protected $signature = 'hostpinnacle:install';

    /** @var string */
    protected $description = 'Install Hostpinnacle Laravel Package';

    /**
     * Publish config; prompt to overwrite if config already exists.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing Laravel Hostpinnacle......');
        $this->info('Publishing hostpinnacle configuration');

        if (!$this->configExists('hostpinnacle.php')) {
            $this->publishConfiguration();
            $this->info('Publishing hostpinnacle configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting hostpinnacle configuration file......');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Exiting. Hostpinnacle configuration was not overwritten');
            }
        }

        $this->info('Installed Hostpinnacle Package');

        return self::SUCCESS;
    }

    /**
     * Check if a config file exists in the application config path.
     *
     * @param  string  $fileName
     * @return bool
     */
    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    /**
     * Ask the user whether to overwrite the existing config file.
     *
     * @return bool
     */
    private function shouldOverwriteConfig()
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    /**
     * Publish the hostpinnacle config via vendor:publish.
     *
     * @param  bool  $forcePublish
     * @return int
     */
    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider",
            '--tag' => "hostpinnacle-config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        return $this->call('vendor:publish', $params);
    }
}
