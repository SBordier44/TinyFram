<?php

namespace App\Shop\Table;

use App\Auth\Entity\User;
use Framework\Database\Table;

class StripeUserTable extends Table
{
    /**
     * @var string
     */
    protected $table = 'users_stripe';
    
    /**
     * @param User $user
     * @return null|string
     */
    public function findCustomerForUser(User $user): ?string
    {
        $record = $this->makeQuery()->where('user_id = :user')->params(['user' => $user->getId()])->fetch();
        if ($record === false) {
            return null;
        }
        return $record->customerId;
    }
}
