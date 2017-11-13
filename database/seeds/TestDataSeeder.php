<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Model\Structure;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $structuresTable = 'ts_structures';
        $sectionsTable = 'ts_sections';
        $bannersTable = 'ts_banners';


        DB::table($structuresTable)->truncate();
        DB::table($structuresTable)->insert($this->getStructureTestData());

        $baseSections = $this->getSectionTestData();
        foreach ($baseSections as $section) {
            $tsStructureId = null;
            $structure = new Structure;
            $structureObj = $structure->set($section['goods_type'], $section['sale_type'], $section['section_file_name'])->getOne();
            if (is_object($structureObj)) {
                if (property_exists($structureObj, 'id')) {
                    $tsStructureId = $structureObj->id;
                }
            }
            $sections[] = [
                'code_type' => $section['code_type'],
                'code' => $section['code'],
                'image_url' => $section['image_url'],
                'ts_structure_id' => $tsStructureId
            ];
        }

        DB::table($sectionsTable)->truncate();
        DB::table($sectionsTable)->insert($sections);

        $baseBanners = $this->getBannerTestData();
        foreach ($baseBanners as $banner) {
            $tsStructureId = null;
            $structure = new Structure;
            $structureObj = $structure->conditionFindBannerWithSectionFileName($banner['section_file_name'])->getOne();
            if (is_object($structureObj)) {
                if (property_exists($structureObj, 'id')) {
                    $tsStructureId = $structureObj->id;
                }
            }
            $banners[] = [
                'link_url' => $banner['link_url'],
                'is_tap_on' => $banner['is_tap_on'],
                'image_url' => $banner['image_url'],
                'ts_structure_id' => $tsStructureId
            ];
        }
        DB::table($bannersTable)->truncate();
        DB::table($bannersTable)->insert($banners);


    }

    private function getStructureTestData() {
        $reader = Reader::createFromPath(base_path().'/tests/fixture/structure.csv', 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        $sortIndex = 1;
        foreach ($records as $record) {
            $structure[] =
                [
                    'sort' => $sortIndex,
                    'goods_type' => $record['goods_type'],
                    'sale_type' => $record['sale_type'],
                    'section_type' => $record['section_type'],
                    'display_start_date' => $record['display_start_date'],
                    'display_end_date' => $record['display_end_date'],
                    'title' => $record['title'],
                    'link_url' => $record['link_url'],
                    'is_tap_on' => $record['is_tap_on'],
                    'is_ranking' => $record['is_ranking'],
                    'api_url'  => $record['api_url'],
                    'section_file_name' => $record['section_file_name'],
                    'banner_width' => $record['banner_width'],
                    'banner_height' => $record['banner_height']
                ];
            $sortIndex++;
        }
        return $structure;
    }

    private function getSectionTestData()
    {
        $reader = Reader::createFromPath(base_path() . '/tests/fixture/section.csv', 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        $sortIndex = 1;
        foreach ($records as $record) {
            $section[] =
                [
                    'goods_type' => $record['goods_type'],
                    'sale_type' => $record['sale_type'],
                    'code_type' => $record['code_type'],
                    'code' => $record['code'],
                    'image_url' => $record['image_url'],
                    'section_file_name' => $record['section_file_name']
                ];
            $sortIndex++;
        }
        return $section;
    }
    private function getBannerTestData()
    {
        $reader = Reader::createFromPath(base_path() . '/tests/fixture/banner.csv', 'r')
            ->setHeaderOffset(0);
        $records = $reader->getRecords();
        $sortIndex = 1;
        foreach ($records as $record) {
            $section[] =
                [
                    'link_url' => $record['link_url'],
                    'is_tap_on' => $record['is_tap_on'],
                    'image_url' => $record['image_url'],
                    'section_file_name' => $record['section_file_name']
                ];
            $sortIndex++;
        }
        return $section;
    }
}
