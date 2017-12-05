<?php
/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 * @license MIT
 */

namespace App\Slack;

class FilesQuery extends AbstractQuery
{
    /**
     * List user's files in a channel
     *
     * @param string $channelId
     * @param string $userId
     *
     * @return \stdClass[]
     */
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

    /**
     * Delete all user'sfiles (with limit) in a channel
     *
     * @param string $channelId
     * @param string $userId
     *
     * @return string[]
     */
    public function deleteAll(string $channelId, string $userId): array
    {
        $files = $this->list($channelId, $userId);

        $deleted = [];
        foreach ($files as $file) {
            $deleted[] = $this->delete($file->id);
        }

        return $deleted;
    }

    /**
     * Delete one file
     *
     * @param string $fileId
     *
     * @return string
     */
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
