<?php

namespace App\Http\Livewire;

class SessionDataManager
{
    /**
     * Store data in the session.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function storeData($key, $value)
    {
        session([$key => $value]);
    }

    /**
     * Retrieve data from the session.
     *
     * @param string $key
     * @return mixed
     */
    public function retrieveData($key)
    {
        return session($key);
    }
}
