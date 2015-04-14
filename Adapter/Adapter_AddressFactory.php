<?php

class Adapter_AddressFactory
{
    public function findOrCreate($id_address = null, $with_geoloc = false)
    {
        return call_user_func_array(array('Address', 'initialize'), func_get_args());
    }

    public function addressExists($id_address)
    {
        return Address::addressExists($id_address);
    }
}
