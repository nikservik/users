<?php


namespace Nikservik\Users\Blessings;

class UserBlessingsChangedListener
{
    public function handle(UserBlessingsChanged $event)
    {
        UpdateBlessings::dispatch($event->user);
    }
}
