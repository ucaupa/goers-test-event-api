<?php

namespace App\Repositories\Contracts;

interface IPengelolaRepository extends IGenericRepository
{
    public function destroy($id);
    public function updatePengelola($id, $data);
}
