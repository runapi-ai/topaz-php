<?php

declare(strict_types=1);

namespace RunApi\Topaz;

/**
 * Constants for model slugs supported by the Topaz PHP SDK.
 */
final class Types
{
    /** @var list<string> */
    public const UPSCALE_IMAGE_MODELS = ['topaz-upscale-image'];

    /** @var list<string> */
    public const UPSCALE_VIDEO_MODELS = ['topaz-upscale-video'];

    private function __construct()
    {
    }
}
