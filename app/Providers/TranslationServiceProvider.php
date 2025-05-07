<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TranslationServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        $filesystem = new Filesystem();
        $loader = new FileLoader(
            $filesystem, rootPath() . '/lang');
            $loader->addNamespace(
                'lang',
                rootPath() . '/lang'
            );
        $loader->load(config('app.language'), 'validation', 'lang');

        $container[Translator::class] = function ($c) use ($loader) {
            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            
            $translator = new Translator($loader, config('app.language'));

            return $translator;
        };
    }
}