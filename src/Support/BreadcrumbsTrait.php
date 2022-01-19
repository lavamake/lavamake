<?php

namespace Lavamake\Lavamake\Support;

use App\Models\Navigation;

trait BreadcrumbsTrait
{
    /**
     * getBreadcrumb
     *
     * @param $lastone_id
     * @param $tree
     *
     * @return array
     *
     * by Menlain
     * 2022/1/18 - 4:04 AM
     */
    public function getBreadcrumb($lastone_id, $tree = [])
    {
        if ($lastone_id === 0) {
            $item = [
                'title' => '首页',
                'path' => url('/')
            ];
            array_unshift($tree, $item);
            return $tree;
        }else {
            $navigation = Navigation::where([
                "id" => $lastone_id
            ])->first();

            $item = [
                'title' => $navigation->title,
                'path' => url('/cat',['unicode'=>$navigation->unicode])
            ];
            array_unshift($tree, $item);
            return $this->getBreadcrumb($navigation->pid, $tree);
        }
    }

}
