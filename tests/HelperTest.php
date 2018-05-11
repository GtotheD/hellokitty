<?php

use tests\TestData;

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
        $this->assertEquals('aaabbbccccdddd\neeee\nfffff', $contents);
    }

}