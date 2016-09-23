<?php

namespace App\Libraries\Interfaces\Registrar;

interface RegistrarAdmin
{
    public function manageDomain($id, $service_id);

    public function registerPurchasedDomain($id, $service_id);
}
