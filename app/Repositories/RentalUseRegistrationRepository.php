<?php

namespace App\Repositories;

use App\Model\MintMemberDetail;

class RentalUseRegistrationRepository extends BaseRepository
{

    public function get()
    {
        $mintMemberDetail = new MintMemberDetail;
        $mintMemberDetailCollection = $mintMemberDetail->getCollection();
        foreach ($mintMemberDetailCollection as $item) {
            var_dump($item['kanaFullName']);
        }
        dd();
    }

}
