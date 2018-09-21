<?php

use tests\TestData;
use Illuminate\Support\Carbon;

class HelperTest extends TestCase
{
    /**
     * StripTagsでタグが変換されることを確認
     * @test
     */
    public function stripTagsTest()
    {
        $contents = 'aaa<p>bbb</p><div>cccc</div><span>dddd</span><br>eeee<br/>fffff';
        $contents = StripTags($contents);
        $this->assertEquals("aaabbbccccdddd\neeee\nfffff", $contents);
    }

    /**
     * newフラグのテスト
     * @test
     */
    public function newFlgTest()
    {
        Carbon::setTestNow(new Carbon('2018-05-05 00:00:00'));
        // 当月当月
        $this->assertTrue(NewFlg('2018-05-05 00:00:00'));
        $this->assertTrue(NewFlg('2018-05-04 00:00:00'));
        $this->assertTrue(NewFlg('2018-05-06 00:00:00'));
        // 先月
        $this->assertTrue(NewFlg('2018-04-05 00:00:00'));
        // 来月
        $this->assertTrue(NewFlg('2018-06-05 00:00:00'));
        // 先月以前 FASLE
        $this->assertFalse(NewFlg('2018-04-04 00:00:00'));
        // 来月以降
        $this->assertFalse(NewFlg('2018-06-06 00:00:00'));
    }

}