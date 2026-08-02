<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Schedule domain: list, update, or cancel SMS scheduled for future delivery.
 */
class ScheduleClient extends BaseApiClient
{
    /**
     * List scheduled messages within a date range.
     *
     * @param  array{fromdate: string, todate: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function read($data)
    {
        if (!isset($data['fromdate']) || !isset($data['todate'])) {
            throw new IsNullException('fromdate and todate must not be null');
        }

        return $this->post('/schedule/read', [
            'fromdate' => $data['fromdate'],
            'todate' => $data['todate'],
        ]);
    }

    /**
     * Reschedule a previously scheduled message.
     *
     * @param  array{uuid: string, scheduletime: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['uuid']) || !isset($data['scheduletime'])) {
            throw new IsNullException('uuid and scheduletime must not be null');
        }

        return $this->post('/schedule/update', [
            'uuid' => $data['uuid'],
            'scheduletime' => $data['scheduletime'],
        ]);
    }

    /**
     * Cancel a scheduled message.
     *
     * @param  array{uuid: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function delete($data)
    {
        if (!isset($data['uuid'])) {
            throw new IsNullException('uuid must not be null');
        }

        return $this->post('/schedule/delete', [
            'uuid' => $data['uuid'],
        ]);
    }
}
