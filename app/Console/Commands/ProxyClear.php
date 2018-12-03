<?php

namespace App\Console\Commands;

use App\Models\Proxy;
use App\Spiders\Tester;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProxyClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:clear {remainder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代理清洗';

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
        //余数
        $remainder = $this->argument('remainder');
        //查询间隔
        $time_limit = Carbon::now()->subMinutes(5);

        $tester = Tester::getInstance();
        $proxies = Proxy::query()
            ->whereRaw("id % 5 = {$remainder}")
            ->where("last_checked_at", "<", $time_limit)
            ->orderBy('updated_at')
            ->take(30)
            ->get();
        $proxies->each(function ($proxy) use ($tester) {
            $tester->handle($proxy);
        });
    }
}