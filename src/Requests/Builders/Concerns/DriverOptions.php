<?php

namespace Aedart\Http\Clients\Requests\Builders\Concerns;

use Aedart\Contracts\Http\Clients\Requests\Builder;
use Aedart\Http\Clients\Requests\Builders\ProcessedOptions;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineInterface;
use Illuminate\Pipeline\Pipeline;

/**
 * Concerns Driver Options
 *
 * @see Builder
 * @see Builder::withOption
 * @see Builder::withOptions
 * @see Builder::withoutOption
 * @see Builder::hasOption
 * @see Builder::getOption
 * @see Builder::getOptions
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Clients\Requests\Builders\Concerns
 */
trait DriverOptions
{
    /**
     * Driver specific options for the next request
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Set a specific option for the next request
     *
     * Method will merge given options with Client's default options
     *
     * @param string $name
     * @param mixed $value
     *
     * @return self
     */
    public function withOption(string $name, $value): Builder
    {
        return $this->withOptions([ $name => $value ]);
    }

    /**
     * Apply a set of options for the next request
     *
     * Method will merge given options with Client's default options
     *
     * @param array $options [optional]
     *
     * @return self
     */
    public function withOptions(array $options = []): Builder
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Remove given option for the next request
     *
     * @param string $name
     *
     * @return self
     */
    public function withoutOption(string $name): Builder
    {
        unset($this->options[$name]);

        return $this;
    }

    /**
     * Determine if a given option exists for the next
     * request
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Get a specific option for the next request
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getOption(string $name)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * Get all the options for the next request
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Prepares this builder, based on given driver specific options.
     *
     * Method MIGHT alter the resulting driver options, depending on
     * circumstance and context.
     *
     * @param array $options [optional] Driver specific options
     *
     * @return array Driver specific options
     */
    protected function prepareBuilderFromOptions(array $options = []): array
    {
        return $options;
    }

    /**
     * Processes the driver's options via given set of pipes
     *
     * Depending on the given pipes and options, both the
     * provided options as well as this builder's properties
     * and state can be mutated by the pipes.
     *
     * @see makePipeline
     * @see \Illuminate\Contracts\Pipeline\Pipeline
     *
     * @param string[] $pipes List of class paths
     * @param array $options [optional]
     *
     * @return array Processed Driver Options
     */
    protected function processDriverOptions(array $pipes, array $options = []): array
    {
        return $this
            ->makePipeline()
            ->send(new ProcessedOptions($this, $options))
            ->through($pipes)
            ->then(function (ProcessedOptions $prepared) {
                return $prepared->options();
            });
    }

    /**
     * Creates a new Pipeline instance
     *
     * @return PipelineInterface
     */
    protected function makePipeline(): PipelineInterface
    {
        return new Pipeline($this->getContainer());
    }
}
