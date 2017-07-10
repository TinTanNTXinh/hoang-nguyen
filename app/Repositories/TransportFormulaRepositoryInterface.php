<?php

namespace App\Repositories;

interface TransportFormulaRepositoryInterface
{
    public function deleteByTransportId($transport_id);

    public function deactivateByTransportId($transport_id);
}