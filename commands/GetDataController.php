<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\City;
use app\models\Street;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GetDataController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $db = Yii::$app->db;
        $secretToken = Yii::$app->params['secretToken'];
        
        $cityTableName = City::tableName();
        $cityColumns = City::getTableSchema()->getColumnNames();
        
        $streetTableName = Street::tableName();
        $streetColumns = Street::getTableSchema()->getColumnNames();
        
        // Create instance of HTTP Client for Yii2
        $client = new Client();
        
        // Get cities
        $cities = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://digital.kt.ua/api/test/cities')
            ->setHeaders(['secret-token' => $secretToken])
            ->send()
            ->getData();
        
        // Remove unnecessary column id
        ArrayHelper::removeValue($cityColumns, 'id');
        
        // Create a query with mass insert or update if records already exists
        $citiesQuery = $db->queryBuilder->batchInsert($cityTableName, $cityColumns, $cities);
        $citiesQuery .= ' ON DUPLICATE KEY UPDATE name = VALUES(name)';
        $db->createCommand($citiesQuery)->execute();
        
        // Create array of cities refs
        $cities_refs = ArrayHelper::getColumn($cities, 'ref');
        
        foreach ($cities_refs as $city_ref) {
            // Get streets for each city
            $streets = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://digital.kt.ua/api/test/streets')
                ->setHeaders(['secret-token' => $secretToken])
                ->setData(['city_ref' => $city_ref])
                ->send()
                ->getData();
            
            // Remove unnecessary column id
            ArrayHelper::removeValue($streetColumns, 'id');
            
            // Add necessary column city_ref to each street
            foreach ($streets as $key => $value) {
                $streets[$key]['city_ref'] = $city_ref;
            }
            
            // Create a query with mass insert or update if records already exists
            $streetsQuery = $db->queryBuilder->batchInsert($streetTableName, $streetColumns, $streets);
            $streetsQuery .= ' ON DUPLICATE KEY UPDATE name = VALUES(name)';
            $db->createCommand($streetsQuery)->execute();
        }
        
        echo 'All is Fine!';
        return ExitCode::OK;
    }
}
