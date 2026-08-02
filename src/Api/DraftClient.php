<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Draft domain: saved title/content pairs to send later.
 */
class DraftClient extends BaseApiClient
{
    /**
     * Create a draft.
     *
     * @param  array{title: string, content: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['title']) || !isset($data['content'])) {
            throw new IsNullException('title and content must not be null');
        }

        return $this->post('/draft/create', [
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    /**
     * List drafts on the account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/draft/read');
    }

    /**
     * Update an existing draft.
     *
     * @param  array{title: string, content: string, id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['title']) || !isset($data['content']) || !isset($data['id'])) {
            throw new IsNullException('title, content and id must not be null');
        }

        return $this->post('/draft/update', [
            'title' => $data['title'],
            'content' => $data['content'],
            'id' => $data['id'],
        ]);
    }

    /**
     * Delete one or more drafts by id (comma-separated).
     *
     * @param  array{id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function delete($data)
    {
        if (!isset($data['id'])) {
            throw new IsNullException('id must not be null');
        }

        return $this->post('/draft/delete', [
            'id' => $data['id'],
        ]);
    }
}
