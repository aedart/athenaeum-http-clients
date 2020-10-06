<?php

namespace Aedart\Http\Clients\Requests\Builders\Concerns;

use Aedart\Contracts\Http\Clients\Requests\Builder;
use Aedart\Support\Helpers\Logging\LogTrait;
use Illuminate\Support\Str;
use Psr\Http\Message\MessageInterface;

/**
 * Concerns Logging
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Clients\Requests\Builders\Concerns
 */
trait Logging
{
    use LogTrait;

    /**
     * Request / Response logging callback
     *
     * @var callable|null
     */
    protected $logCallback = null;

    /**
     * @inheritDoc
     */
    public function log(?callable $callback = null): Builder
    {
        $callback = $callback ?? $this->makeLogCallback();

        return $this->setLogCallback($callback);
    }

    /**
     * @inheritDoc
     */
    public function logCallback(): callable
    {
        if (!isset($this->logCallback)) {
            return $this->makeNullLogCallback();
        }

        return $this->logCallback;
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Set the request / response logging callback to be
     * applied.
     *
     * @param  callable  $callback
     *
     * @return Builder
     */
    protected function setLogCallback(callable $callback): Builder
    {
        $this->logCallback = $callback;

        return $this;
    }

    /**
     * Returns a "null" log callback method.
     *
     * @return callable
     */
    protected function makeNullLogCallback(): callable
    {
        return function (string $type, MessageInterface $message, Builder $builder) {
            // N/A...
        };
    }

    /**
     * Returns a default logging callback
     *
     * @return callable
     */
    protected function makeLogCallback(): callable
    {
        return function (string $type, MessageInterface $message, Builder $builder) {
            $this->getLog()->info(
                Str::ucfirst($type),
                $this->makeDebugContext($type, $message)
            );
        };
    }
}
