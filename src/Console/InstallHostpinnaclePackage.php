<?php

namespace Itsmurumba\Hostpinnacle\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallHostpinnacle extends Command
{
    protected $signature = 'hostpinnacle:install';

    protected $description = 'Install Hostpinnacle Laravel Package';

    public function handle()
    {
        $this->info('Installing Laravel Mpesa......');
        $this->info('Publishing hostpinnacle configuration');

        if (!$this->configExists('hostpinnacle.php')) {
            $this->publishConfiguration();
            $this->info('Publishing hostpinnacle configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwitting hostpinnacle configuration file......');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Exiting. Hostpinnacle configuration was not overwritten');
            }
        }

        $this->info('Installed Hostpinnacle Package');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider",
            '--tag' => "hostpinnacle-config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
