<?php
use App\Repositories\FavoriteRepository;

class FavoriteRepositoryTest extends TestCase
{
    private $apiPath;

    public function setUp()
    {
        $this->setIsSetUp();
        parent::setUp();
        $this->baseUrl = env('APP_URL').'/'.env('URL_PATH_PREFIX').env('API_VERSION');
    }

    /**
     * test api list return response status 200
     * @return type
     */
    public function testList()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/LIST-API01.json');
       $url = '/favorite/list';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(200);
    }

    /**
     * test api favorite list if no tlsc input
     * @return true or false
     */
    public function testListNoTlsc()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/LIST-API01-no-tlsc.json');
       $url = '/favorite/list';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(400);
    }

    /**
     * test api favorite list 
     * version inpunt from file have equal version so isUpdate return false
     * @return type
     */
    public function testListResponseNoUpdate()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/LIST-API01-no-update.json');
       $url = '/favorite/list';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       // Get current version
       $favoriteRepository = new FavoriteRepository;
       $currentVersion = $favoriteRepository->getFavoriteVersion($jsondata['tlsc']);
       $jsondata['version'] = $currentVersion;
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['isUpdate' => false]);
    }

    /**
     * test api bulk return response status 200
     * @return type
     */
    public function testBulk()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/BULK-API01.json');
       $url = '/work/bulk';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(200);
    }

    /**
     * test api bulk
     * Check api return must have totalCount
     * @return type
     */
    public function testBulkResponse()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/BULK-API01.json');
       $url = '/work/bulk';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['totalCount' => 30]);
    }

    /**
     * test api add return response status 200
     * @return type
     */
    public function testAdd()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/ADD-API01.json');
       $url = '/favorite/add';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(200);
    }

    /**
     * test api add return status success
     * @return type
     */
    public function testAddSucces()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/ADD-API01.json');
       $url = '/favorite/add';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'success']);
    }

    /**
     * test api add return status error
     * @return type
     */
    public function testAddFail()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/ADD-API01-fail.json');
       $url = '/favorite/add';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'error']);
    }

    /**
     * test api merge return response status 200
     * @return type
     */
    public function testMerge()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/MERGE-API01.json');
       $url = '/favorite/add/merge';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(200);
    }

    /**
     * test api merge return status success
     * @return type
     */
    public function testMergeSuccess()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/MERGE-API01.json');
       $url = '/favorite/add/merge';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'success']);
    }

    /**
     * test api merge return status fail
     * @return type
     */
    public function testMergeFail()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/MERGE-API01-fail.json');
       $url = '/favorite/add/merge';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'error']);
    }

    /**
     * test api delete return response status 200
     * @return type
     */
    public function testDelete()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/DELETE-API01.json');
       $url = '/favorite/delete';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $response = $this->postJsonWithAuth($url, $jsondata);
       $response->assertResponseStatus(200);
    }

    /**
     * test api delete return status success
     * @return type
     */
    public function testDeleteSuccess()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/DELETE-API01.json');
       $url = '/favorite/delete';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'success']);
    }

    /**
     * test api delete return status fail
     * @return type
     */
    public function testDeleteFail()
    {  
       $jsondata = file_get_contents('tests/Data/favorite/DELETE-API01-fail.json');
       $url = '/favorite/delete';
       // Convert string to array
       $jsondata = (array) json_decode($jsondata);
       $this->postJsonWithAuth($url, $jsondata)->seeJson(['status' => 'error']);
    }
}