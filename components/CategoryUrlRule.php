<?php

namespace panix\mod\docs\components;

use panix\mod\docs\models\Docs;
use yii\web\UrlRule;

class CategoryUrlRule extends UrlRule
{

   // public $pattern = 'docs/default/view';
    public $route = 'docs/default/view';

    public function createUrl2($manager, $route, $params)
    {
        if ($route === 'docs/default/view') {

            if (isset($params['seo_alias'])) {
                $url = trim($params['seo_alias'], '/');
                unset($params['seo_alias']);
            } else {
                $url = '';
            }
            $parts = [];
            if (!empty($params)) {
                foreach ($params as $key => $val) {
                    $parts[] = $key . '/' . $val;
                }
                $url .= '/' . implode('/', $parts);
            }

            return $url . $this->suffix;
        }

        return false;
    }



    public function parseRequest($manager, $request)
    {
        $params = [];
        $pathInfo = $request->getPathInfo();

        if (empty($pathInfo))
            return false;

        if ($this->suffix)
            $pathInfo = strtr($pathInfo, [$this->suffix => '']);

        $pathInfo = str_replace('docs/', '', $pathInfo);

        foreach ($this->getAllPaths() as $path) {
            if ($path['full_path'] !== '' && strpos($pathInfo, $path['full_path']) === 0) {
                $_GET['seo_alias'] = $path['full_path'];
                $uri = str_replace($path['full_path'], '', $pathInfo);
                $parts = explode('/', $uri);
                unset($parts[0]);
                //$pathInfo = implode($parts, '/');
                //   print_r(array_chunk($parts, 2));
                $ss = array_chunk($parts, 2);
                foreach ($ss as $k => $p) {
                    // print_r($p);
                    $_GET[$p[0]] = $p[1];
                    $params[$p[0]] = $p[1];
                }

                $params['seo_alias'] = ltrim($path['full_path']);
                return ['docs/default/view', $params];
            }
        }
        return false;
    }

    protected function getAllPaths()
    {
        $allPaths = \Yii::$app->cache->get(__CLASS__);
        if ($allPaths === false) {
            $allPaths = (new \yii\db\Query())
                ->select(['full_path'])
                ->andWhere('id!=:id', [':id' => 1])
                ->from(Docs::tableName())
                ->all();


            // Sort paths by length.
            usort($allPaths, function ($a, $b) {
                return strlen($b['full_path']) - strlen($a['full_path']);
            });

            \Yii::$app->cache->set(__CLASS__, $allPaths,1);
        }

        return $allPaths;
    }

}
