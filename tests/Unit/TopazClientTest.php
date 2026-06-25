<?php

declare(strict_types=1);

namespace RunApi\Topaz\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Topaz\Models\CompletedImageTaskResponse;
use RunApi\Topaz\Resources\UpscaleImage;
use RunApi\Topaz\Resources\UpscaleVideo;
use RunApi\Topaz\TopazClient;

final class TopazClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(UpscaleImage::class, $client->upscaleImage);
        self::assertInstanceOf(UpscaleVideo::class, $client->upscaleVideo);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
        ]);
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->upscaleImage->create([
            'model' => 'topaz-upscale-image',
            'source_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'upscale_factor' => 1,
            'callback_url' => '',
            'seed' => null,
        ]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/topaz/upscale_image', $transport->requests[0]->getUri()->getPath());
        self::assertSame('topaz-upscale-image', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","images":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept"}'),
        ]);
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->upscaleImage->run([
            'model' => 'topaz-upscale-image',
            'source_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'upscale_factor' => 1,
        ]);

        self::assertInstanceOf(CompletedImageTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->images[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/topaz/upscale_image/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed"}'),
        ]);
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images is required');

        $client->upscaleImage->run([
            'model' => 'topaz-upscale-image',
            'source_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'upscale_factor' => 1,
        ]);
    }

    public function testRejectsInvalidContractEnum(): void
    {
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('upscale_factor must be one of the allowed values');

        $client->upscaleImage->create([
        'model' => 'topaz-upscale-image',
        'source_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
        'upscale_factor' => 3,
        ]);
    }

    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}'),
        ]);
        $client = new TopazClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->upscaleVideo->create([
            'model' => 'topaz-upscale-video',
            'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
            'upscale_factor' => 1,
        ]);

        self::assertSame('/api/v1/topaz/upscale_video', $transport->requests[0]->getUri()->getPath());
    }
}
