<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Symfony\Component\Console\Output\Output;
use Carbon\Carbon;

class UserUpdate extends Command
{
    // Kayıtları sayfalama yaparak günceller.

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:column-update  {--count=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user column update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userCount = $this->query()->count();
        $perPage = $this->option('count');
        $pageCount = ceil($userCount / $perPage);

        $start = microtime(true);
        for ($page = 0; $page < $pageCount; $page++) {
            $users = $this->getUsers($page, $perPage);
            /** @var User $user */
            foreach ($users as $user) {
                //User update
                $year = rand(Carbon::now()->subYear(24)->format("Y"), Carbon::now()->subYear(18)->format("Y"));
                $month = rand(1,12);
                $day = rand(1,28);
                $user->temporary_birthday = Carbon::parse($year."-".$month."-".$day)->format("Y-m-d");
                $user->save();
            }

            if ($this->getOutput()->getVerbosity() >= Output::VERBOSITY_DEBUG) {
                $this->info(
                    $page . '/' . $pageCount
                    . ' - ' . $this->memoryUsage()
                    . ' - ' . (int)((microtime(true) - $start))
                );
            }
        }
    }

    private function query()
    {
        return User::query()
            ->whereNull('birthday')
            ->whereNull('temporary_birthday');
    }

    private function getUsers($page, $perPage)
    {
        return $this->query()
            ->take($perPage)
            //->skip($page * $perPage) // Bu kod ile daha önce işlem yapılan kayıtları atlayarak işlem yapar.
            ->orderBy('id', 'asc')
            ->get();
    }

    function memoryUsage()
    {
        $size = memory_get_usage();
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}
