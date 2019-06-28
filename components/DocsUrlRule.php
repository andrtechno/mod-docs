<?php

namespace panix\mod\docs\components;

use yii\web\UrlRule;
use panix\mod\docs\models\Docs;

/**
 * Class DocsUrlRule
 * @package panix\mod\docs\components
 */
class DocsUrlRule extends UrlRule
{

    public $pattern = '/docs/<slug:[0-9a-zA-Z\-]+>';
    public $route = 'docs/default/view';

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        if ($route === $this->route) {
            if (isset($params['slug'])) {
                $url = 'docs/' . trim($params['slug'], '/');
                unset($params['slug']);
            } else {
                $url = 'docs/';
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
        return parent::createUrl($manager, $route, $params);
    }


    /**
     * @inheritdoc
     */
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
                $_GET['slug'] = $path['full_path'];
                $uri = str_replace($path['full_path'], '', $pathInfo);
                $parts = explode('/', $uri);

                unset($parts[0]);

                //$pathInfo = implode($parts, '/');
                //   print_r(array_chunk($parts, 2));
                $ss = array_chunk($parts, 2);



                foreach ($ss as $k => $p) {
                    // print_r($p);
                    if (isset($p[0])) {
                        $_GET[$p[0]] = $p[0];
                        $params[$p[0]] = $p[0];
                    }
                }

                $params['slug'] = ltrim($path['full_path']);
                return [$this->route, $params];
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
                ->from(Docs::tableName())
                ->all();


            // Sort paths by length.
            usort($allPaths, function ($a, $b) {
                return strlen($b['full_path']) - strlen($a['full_path']);
            });

            \Yii::$app->cache->set(__CLASS__, $allPaths, 1);
        }

        return $allPaths;
    }

}
