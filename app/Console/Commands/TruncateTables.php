<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Model\Work;
use App\Model\Product;
use App\Model\PeopleRelatedWork;
use App\Model\RelatedPeople;
use App\Model\RelateadWork;
use App\Model\Series;
use App\Model\MusicoUrl;
use App\Model\DiscasProduct;

class TruncateTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TruncateTables';

    /**
     * /**
     * Create a new command instance.
     **/
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
        $this->info('Truncate Start.');
        $tables = [
            Work::TABLE,
            Product::TABLE,
            PeopleRelatedWork::TABLE,
            RelatedPeople::TABLE,
            RelateadWork::TABLE,
            Series::TABLE,
            MusicoUrl::TABLE,
            DiscasProduct::TABLE
        ];

        foreach ($tables as $table) {
            $this->info('Truncate　→　'. $table);
            DB::table($table)->truncate();
        }
        $this->info('Finish Truncate.');
        return true;
    }

}
