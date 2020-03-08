<?php

namespace Aedart\Http\Clients\Requests\Builders\Guzzle\Pipes;

use Aedart\Http\Clients\Requests\Builders\Guzzle\PayloadData;
use Aedart\Http\Clients\Requests\Builders\PreparedOptions;
use GuzzleHttp\RequestOptions;

/**
 * Resolves Request Payload
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Clients\Requests\Builders\Guzzle\Pipes
 */
class ResolvesRequestPayload
{
    /**
     * Sets the request's payload data, via the options
     *
     * @param PreparedOptions $prepared
     * @param mixed $next
     *
     * @return mixed
     */
    public function handle(PreparedOptions $prepared, $next)
    {
        // Get options set from the builder
        $options = $prepared->preparedOptions();

        // Obtain evt. appended data (e.g. via the post, patch, ...etc)
        // Or perhaps via the withOptions() method on the builder.
        $data = PayloadData::extract($options);

        // Unset the payload data on the options (will be added again later)
        unset(
            $options[RequestOptions::BODY],
            $options[RequestOptions::JSON],
            $options[RequestOptions::MULTIPART],
            $options[RequestOptions::FORM_PARAMS],
        );

        // Determine the desired data format
        $format = $options['data_format'] ?? $prepared->builder()->getDataFormat();
        unset($options['data_format']);

        // Finally, apply the data
        $options[$format] = $data;
        $prepared->setPreparedOptions($options);

        return $next($prepared);
    }
}