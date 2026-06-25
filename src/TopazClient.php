<?php

declare(strict_types=1);

namespace RunApi\Topaz;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Topaz\Resources\UpscaleImage;
use RunApi\Topaz\Resources\UpscaleVideo;

/**
 * Provides AI-powered upscaling for both images and videos.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class TopazClient extends BaseClient
{
    /**
     * Upscale image operations.
     */
    public readonly UpscaleImage $upscaleImage;
    /**
     * Upscale video operations.
     */
    public readonly UpscaleVideo $upscaleVideo;

    /**
     * Create a Topaz client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->upscaleImage = UpscaleImage::fromHttp($this->http);
        $this->upscaleVideo = UpscaleVideo::fromHttp($this->http);
    }
}
