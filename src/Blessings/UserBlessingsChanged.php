<?php


namespace Nikservik\Users\Blessings;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Событие изменения благословений у пользователя.
 * Вызывается, когда, например, у пользователя меняется статус подписки.
 */
class UserBlessingsChanged
{
    use Dispatchable;
    use SerializesModels;

    public User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
