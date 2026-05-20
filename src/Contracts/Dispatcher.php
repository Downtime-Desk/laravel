<?php

namespace DowntimeDesk\Laravel\Contracts;

interface Dispatcher
{
    /**
     * Dispatch the ping using configured methodology.
     *
     * @return mixed
     */
    public function dispatch(string $id, ?string $secret = null);

    /**
     * Dispatches the ping immediately bypassing any configured aggregation or queuing.
     *
     * @return mixed
     */
    public function dispatchNow(string $id, ?string $secret = null);
}
