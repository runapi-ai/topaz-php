<?php

declare(strict_types=1);

namespace RunApi\Topaz\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Topaz\Models\CompletedVideoTaskResponse;
use RunApi\Topaz\Models\VideoTaskResponse;
use RunApi\Topaz\Types;

/**
 * Increases video resolution using AI enhancement. Supports upscale factors of 1x, 2x, and 4x.
 */
readonly class UpscaleVideo extends TypedConfiguredResource
{
    /**
     * Submits a video-upscale task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   source_video_url: string,
     *   callback_url?: string,
     *   upscale_factor?: int
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of a video-upscale task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): VideoTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var VideoTaskResponse $response */
        return $response;
    }

    /**
     * Submits a video-upscale task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   source_video_url: string,
     *   callback_url?: string,
     *   upscale_factor?: int
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedVideoTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedVideoTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/topaz/upscale_video',
            'topaz/upscale-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
            Types::UPSCALE_VIDEO_MODELS,
            'upscale-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
        );
    }
}
