<?php

namespace Juicebox\Apistacker\Commands;

use Illuminate\Console\Command;
use Juicebox\Apistacker\ApistackerServiceProvider;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apistacker:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the required files for the api starter kit.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Publishing assets and configurations... 🍪');

        // Install publishables
        $this->call('vendor:publish', ['--provider' => ApistackerServiceProvider::class, '--tag' => 'apistacker', '--force']);

        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $composerChecks = [
            'LaRecipe Docs' => 'binarytorch/larecipe',
            'LaRecipe Swagger' => 'binarytorch/larecipe-swagger'
        ];

        foreach($composerChecks as $name => $lib){
            if(!array_key_exists($lib, data_get($composerJson, 'require', []))){
                $this->line('Adding '.$name.' to the install... 🍪');

                $command = $composer.' require '.$lib;
                $composer = $this->findComposer();
                $process = new Process(app()::VERSION[0]>6  ? [$command] : $command);
                $process->setTimeout(null);
                $process->setWorkingDirectory(base_path())->run();

                if($lib === 'binarytorch/larecipe'){
                    $this->line('Installing '.$name.'... 🍪');
                    $this->call('larecipe:install');
                }
            }
        }

        // Update postman files
        $replacements = [
            '<name>' => config('app.name'),
            '<url>' => config('app.url'),
            '<app-email>' => 'web+apiuser@juicebox.com.au',
            '<app-password>' => Str::random(40)
        ];

        $files = ['postman_collection.json', 'postman_environment.json'];
        foreach($files as $file){
            if(File::exists(base_path('postman/'.$file))){
                $contents = str_replace(array_keys($replacements), $replacements, File::get(base_path('postman/'.$file)));
                File::replace(base_path('postman/'.$file), $contents);

                $newName = $file === 'postman_environment.json' ? config('app.name').' - '.config('app.env').'.'.$file: config('app.name').'.'.$file
                File::move(base_path('postman/'.$file), base_path('postman/'.$newName))
            }
        }

        $this->info('All done! Go build all the things 😍');

        return 0;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }
}
