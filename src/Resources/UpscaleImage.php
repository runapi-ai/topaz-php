<?php

declare(strict_types=1);

namespace RunApi\Topaz\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Topaz\Models\CompletedImageTaskResponse;
use RunApi\Topaz\Models\ImageTaskResponse;
use RunApi\Topaz\Types;

/**
 * Increases image resolution using AI enhancement. Supports upscale factors of 1x, 2x, and 4x.
 */
readonly class UpscaleImage extends TypedConfiguredResource
{
    /**
     * Submits an image-upscale task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   source_image_url: string,
     *   callback_url?: string,
     *   upscale_factor: int
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of an image-upscale task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): ImageTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var ImageTaskResponse $response */
        return $response;
    }

    /**
     * Submits an image-upscale task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   source_image_url: string,
     *   callback_url?: string,
     *   upscale_factor: int
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedImageTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedImageTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/topaz/upscale_image',
            'topaz/upscale-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
            Types::UPSCALE_IMAGE_MODELS,
            'upscale-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
        );
    }
}
