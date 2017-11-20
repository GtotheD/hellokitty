<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/11/09
 * Time: 16:32
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SectionRepository;
use App\Repositories\TWSRepository;
use App\Model\Section;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import from json data.';

    /**
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
        $this->importStructureData();

        $this->updateSerctionsData();

    }

    private function importStructureData() {


    }

    private function updateSerctionsData() {
        $sectionRepository = new SectionRepository;
        $section = new Section;
        $sections = $section->conditionNoUrlCode()->get(10000);
        $tws = new TWSRepository;
        foreach ($sections as $sectionRow) {
            $this->info($sectionRow->id);
            if (!empty($sectionRow->code)) {
                $res = $tws->detail($sectionRow->code)->get()['entry'];
                $updateValues = [
                    'title' => $res['productName'],
                    'image_url' => $res['image']['large'],
                    'url_code' => $res['urlCd'],
                    'sale_start_date' => $res['saleDate'],
                ];
                if (array_key_exists('artistInfo', $res)) {
                    $updateValues['supplement'] = $sectionRepository->getOneArtist($res['artistInfo'])['artistName'];
                } else if (array_key_exists('modelName', $res)) {
                    $updateValues['supplement'] = $res['modelName'];

                }
                $section->update($sectionRow->id, $updateValues);
            }
        }
    }
}
