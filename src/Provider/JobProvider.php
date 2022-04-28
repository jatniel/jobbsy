<?php

namespace App\Provider;

final class JobProvider implements JobProviderInterface
{
    /**
     * @var JobProviderInterface[]
     */
    private readonly iterable $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobs = new JobCollection();

        foreach ($this->providers as $provider) {
            if (false === $provider->enabled()) {
                continue;
            }

            $jobs->addJob(...$provider->retrieve($parameters)->all());
        }

        return $jobs;
    }

    public function enabled(): bool
    {
        return true;
    }
}