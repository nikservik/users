<?php


namespace Nikservik\Users\Blessings;


trait SingleSubscriptionBlessings
{

    /**
     * Аксессор-обертка для исключительной подписки из пакета subscriptions
     * Чтобы использовать такую подписку как источник благословений,
     * достаточно добавить 'singleSubscription' в свойство $blesserContainers:
     * protected static array $blesserContainers = ['singleSubscription'];
     * @return array|Subscription[]
     */
    public function getSingleSubscriptionAttribute(): array
    {
        return [$this->subscription];
    }

}
