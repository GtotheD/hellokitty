<?php

namespace App\Repositories;

use App\Model\MintMemberDetail;

class RentalUseRegistrationRepository extends BaseRepository
{

    public function get()
    {
        $mintMemberDetail = new MintMemberDetail;
        dd($mintMemberDetail->getClient());
    }

}
