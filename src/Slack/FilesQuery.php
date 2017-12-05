<?php

namespace App\Slack;

use Symfony\Component\Yaml\Exception\RuntimeException;

class FilesQuery extends AbstractQuery
{
    public function list(string $channelId, string $userId): array
    {
        $response = $this->client->post(
            'files.list',
            ['channel' => $channelId, 'user' => $userId],
            null
        );

        $files = $response->files;

        return $files;
    }

    public function deleteAll(string $channelId, string $userId): array
    {
        $files = $this->list($channelId, $userId);

        $deleted = [];
        foreach ($files as $file) {
            $deleted[] = $this->delete($file->id);
        }

        return $deleted;
    }

    public function delete(string $fileId): string
    {
        $this->client->post(
            'files.delete',
            ['file' => $fileId],
            null
        );

        return $fileId;
    }
}
