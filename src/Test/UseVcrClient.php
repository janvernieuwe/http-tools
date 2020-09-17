<?php

declare(strict_types=1);

namespace Phpro\HttpTools\Test;

use Http\Client\Common\PluginClient;
use Http\Client\Plugin\Vcr\NamingStrategy\NamingStrategyInterface;
use Http\Client\Plugin\Vcr\NamingStrategy\PathNamingStrategy;
use Http\Client\Plugin\Vcr\Recorder\FilesystemRecorder;
use Http\Client\Plugin\Vcr\RecordPlugin;
use Http\Client\Plugin\Vcr\ReplayPlugin;
use Psr\Http\Client\ClientInterface;
use Webmozart\Assert\Assert;

trait UseVcrClient
{
    /**
     * @return [RecordPlugin, ReplayPlugin]
     */
    private function useRecording(string $path, NamingStrategyInterface $namingStrategy = null)
    {
        Assert::classExists(RecordPlugin::class, 'Could not find the VCR plugin. Please run: "composer require --dev php-http/vcr-plugin"');

        Assert::directory($path);
        $recorder = new FilesystemRecorder($path);
        $namingStrategy ??= new PathNamingStrategy();

        return [
            new RecordPlugin($namingStrategy, $recorder),
            new ReplayPlugin($namingStrategy, $recorder),
        ];
    }

    /**
     * @param [RecordPlugin, ReplayPlugin] $recording
     */
    public function addRecordingToClient(ClientInterface $client, array $recording): ClientInterface
    {
        return new PluginClient($client, [...$recording]);
    }
}