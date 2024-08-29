<?php

namespace Juicebox\Apistacker\Commands;

use Illuminate\Console\Command;
use Juicebox\Apistacker\ApistackerServiceProvider;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        list($laravel_version, $minor, $patch) = explode('.', app()::VERSION);

        if($laravel_version >= 11){
            $this->line('Installing Laravel api starter kit... 🍪');
            $this->call('install:api', ['--without-migration-prompt' => true, '-n' => true]);

            $this->line('Publishing CORS configuration (CJ\'s favourite)... 🍪');
            $this->call('config:publish', ['name' => 'cors']);


        }
        $this->line('Publishing assets and configurations... 🍪');

        if(File::exists(base_path('config/cors.php'))){
            $this->line('Enabling supports_credentials in CORS... 🍪');
            $content = File::get(base_path('config/cors.php'));
            $replacements = [
                "'supports_credentials' => false" => "'supports_credentials' => true",
            ];
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            File::replace(base_path('config/cors.php'), $content);
        }

        // Install publishables
        $this->call('vendor:publish', ['--provider' => ApistackerServiceProvider::class, '--tag' => 'apistacker', '--force']);

        // Add ForceJsonResponse to Kernel
        if(File::exists(app_path('Http/Kernel.php'))){
            $content = File::get(app_path('Http/Kernel.php'));
            $replacements = ["'throttle:api'," => "'throttle:api'," . PHP_EOL . '            \App\Http\Middleware\ForceJsonResponse::class,'];
            if(stripos($content, 'ForceJsonResponse::class') === false){
                $this->line('Adding ForceJsonResponse to the Http Kernel... 🍪');
                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
                File::replace(app_path('Http/Kernel.php'), $content);
            }
        }
        if(File::exists(base_path('bootstrap/app.php'))){
            $content = File::get(base_path('bootstrap/app.php'));

            $uses = $this->getSnippet('bootstrap_app_uses');
            $apiMiddleware = $this->getSnippet('bootstrap_app_middleware');
            $apiExeptions = $this->getSnippet('bootstrap_app_exceptions');

            if(stripos($content, 'ForceJsonResponse::class') === false){
                $this->line('Adding ForceJsonResponse to the Application middleware... 🍪');
                $replacements = [
                    'use Illuminate\Foundation\Application;' => 'use Illuminate\Foundation\Application;' . PHP_EOL . $uses,
                    'function (Middleware $middleware) {' => 'function (Middleware $middleware) {' . PHP_EOL . $apiMiddleware,
                ];
                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
                File::replace(base_path('bootstrap/app.php'), $content);
            }

            if(stripos($content, 'instanceof AuthenticationException') === false){
                $this->line('Adding API Exceptions to the Application... 🍪');
                $replacements = [
                    'function (Exceptions $exceptions) {' => 'function (Exceptions $exceptions) {' . PHP_EOL . $apiExeptions,
                ];
                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
                File::replace(base_path('bootstrap/app.php'), $content);
            }
        }

        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $composerChecks = [
            'LaRecipe Docs' => 'binarytorch/larecipe',
            'LaRecipe Swagger' => 'binarytorch/larecipe-swagger'
        ];

        foreach($composerChecks as $name => $lib){
            if(!array_key_exists($lib, data_get($composerJson, 'require', []))){
                $this->line('Adding '.$name.' to the install... 🍪');

                $composer = $this->findComposer();
                $command = $composer.' require '.$lib;
                $process = new Process($laravel_version>6  ? [$composer, 'require', $lib] : $command);
                try {
                    $process->setTimeout(300);
                    $process->setWorkingDirectory(base_path())->mustRun();

                    $process = new Process($laravel_version>6  ? [$composer, 'dump-autoload'] : $composer. ' dump-autoload');
                    $process->setTimeout(300);
                    $process->setWorkingDirectory(base_path())->mustRun();

                    if($lib === 'binarytorch/larecipe'){
                        // Running via another process as this->call wasn't working after we added from composer.
                        $this->line('Installing '.$name.'... 🍪');
                        $command = 'php artisan larecipe:install';
                        $process = new Process($laravel_version>6  ? explode(' ', $command) : $command);
                        $process->setTimeout(300);
                        $process->setWorkingDirectory(base_path())->mustRun();
                    }
                } catch (ProcessFailedException $exception) {
                    $this->warn('Unable to install '.$name);
                    $this->warn($exception->getMessage());
                }
            }
        }

        // Setup routes
        $routes = ['api.php'];
        foreach($routes as $route){
            $content = File::get(base_path('routes/'.$route));
            if(stripos($content, '::fallback') === false && File::exists(base_path('routes/'.$route)) && File::exists(__DIR__.'/../../routes/'.$route)){
                $this->line('Updating '.$route.' routes ... 🍪');

                $contents = str_replace(['<?php', '?>'], '', File::get(__DIR__.'/../../routes/'.$route));
                File::append(base_path('routes/'.$route), PHP_EOL . $contents);
            }
        }

        // Update postman files
        $replacements = [
            '<name>' => config('app.name'),
            '<url>' => config('app.url'),
            '<app-email>' => 'web+apiuser@juicebox.com.au',
            '<app-password>' => Str::random(40)
        ];
        $user = User::where('email', $replacements['<app-email>'])->first();

        // Create the user
        if(!$user){
            User::create([
                'name' => 'API User',
                'email' => $replacements['<app-email>'],
                'password' => Hash::make($replacements['<app-password>']),
            ]);
            $this->line('Creating user for the API... 🍪');
        }else{
            $user->password = Hash::make($replacements['<app-password>']);
            $user->save();
        }

        $this->line('Setting up postman collection/environment... 🍪');
        $files = ['postman_collection.json', 'postman_environment.json'];
        foreach($files as $file){
            if(File::exists(base_path('postman/'.$file))){
                $replacements['<name>'] = $file === 'postman_environment.json' ? config('app.name').' - '.config('app.env') : config('app.name');
                $newFileName = $file === 'postman_environment.json' ? config('app.name').' - '.config('app.env').'.'.$file : config('app.name').'.'.$file;
                $contents = str_replace(array_keys($replacements), $replacements, File::get(base_path('postman/'.$file)));

                File::replace(base_path('postman/'.$file), $contents);
                File::move(base_path('postman/'.$file), base_path('postman/'.$newFileName));
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

    /**
     * Get code snippet from file
     *
     * @param string $name
     * @return string|null
     */
    protected function getSnippet(string $name): ?string
    {
        $path = __DIR__.'/../Snippets/'.$name.'.snippet';
        return File::exists($path) ? File::get($path) : null;
    }
}
