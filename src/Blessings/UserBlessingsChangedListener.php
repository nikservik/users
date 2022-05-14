<?php


namespace Nikservik\Users\Blessings;

/**
 * Слушатель для события UserBlessingsChanged.
 * Добавляет в очередь задачу UpdateBlessings.
 */
class UserBlessingsChangedListener
{
    public function handle(UserBlessingsChanged $event)
    {
        UpdateBlessings::dispatch($event->user);
    }
}
