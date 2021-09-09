<?php

namespace App\Policies\User;

use App\Models\User\Address;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function own(User $user, Address $address)
    {
        return $user->id === $address->user_id;
    }
}
