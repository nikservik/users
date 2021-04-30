<?php


namespace Nikservik\Users\Blessings;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;

class UpdateBlessings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    protected array $blessings;
    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->blessings = [];

        foreach ($this->user->getBlessers() as $blesser) {
            $this->addBlessings($blesser->getBlessings());
        }

        $this->user->setBlessings($this->blessings);

        $this->user->save();
    }

    protected function addBlessings(array $blessings): void
    {
        // TODO добавить обработку запрещающих правил

        foreach ($blessings as $blessing) {
            if (! in_array($blessing, $this->blessings)) {
                $this->blessings[] = $blessing;
            }
        }
    }
}
