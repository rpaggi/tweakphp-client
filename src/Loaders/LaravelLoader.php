<?php

namespace TweakPHP\Client\Loaders;

use TweakPHP\Client\Helpers\ClassAliasAutoloader;

class LaravelLoader extends ComposerLoader
{
    private $app;
    private $loader;
    private $path;

    public static function supports(string $path): bool
    {
        return file_exists($path.'/vendor/autoload.php') && file_exists($path.'/bootstrap/app.php');
    }

    public function init(): void
    {   
        parent::init();
        
        $this->loader = ClassAliasAutoloader::register(
            $this->getShell(), $this->path."/vendor/composer/autoload_classmap.php"
        );
    }

    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->app = require_once $path.'/bootstrap/app.php';
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        $this->path = $path;
    }

    public function execute($phpCode): string
    {
        try {
            return parent::execute($phpCode);
        } finally {
            $this->loader->unregister();
        }
    }

    public function name(): string
    {
        return 'Laravel';
    }

    public function version(): string
    {
        return $this->app->version();
    }
}
