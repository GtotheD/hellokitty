<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Model\Structure;

class TestDataKeywordSuggestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = 'ts_himo_keywords1';
        DB::table($table)->truncate();
        $insertData = [
            [
            'keyword' => '貴方',
            'roman_alphabet' => 'anata',
            'hiragana' => 'あなた',
            'katakana' => 'アナタ',
            'created_at' => date('Y-m-d h:m:s'),
            'updated_at' => date('Y-m-d h:m:s')
            ],
            [
                'keyword' => '愛情',
                'roman_alphabet' => 'aijo',
                'hiragana' => 'あいじょう',
                'katakana' => 'アイジョウ',
                'created_at' => date('Y-m-d h:m:s'),
                'updated_at' => date('Y-m-d h:m:s')
            ]
        ];
        DB::table($table)->insert($insertData);

    }
}
