<?php

namespace Disclaimer\Providers;

class DisclaimerServiceProvider implements Provider
{
    protected function providers()
    {
        return [
            OptionsServiceProvider::class,
            DisclaimerFieldsServiceProvider::class,
            TemplateServiceProvider::class,
        ];
    }

    public function register()
    {
        foreach ($this->providers() as $service) {
            (new $service)->register();
        }
    }

    public function boot()
    {
        //
    }
}
