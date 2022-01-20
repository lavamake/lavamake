<?php

namespace Lavamake\Lavamake\Navigation;

use Lavamake\Lavamake\Utils\Consts;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NavigationForWeb extends NavigationAbstract
{
    /**
     * renderRoot
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function renderNavRoot($user_id = 0)
    {
        return $this->renderNavs(0,true,$user_id);
    }

    /**
     * renderRootWithHtml
     *
     * @param $user_id
     *
     * @return string
     */
    public function renderNavRootWithHtml($user_id = 0)
    {
        $unicode = $this->request->route('unicode');

        $condition = $this->condition([
            "unicode" => $unicode,
        ], $user_id);

        $navigation_id = $this->navigation->where($condition)->value('id');
        $breadcrumb = [];
        if ($navigation_id) {
            $breadcrumb = $this->breadcrumb($navigation_id, []);
        }

        $navs = $this->renderNavRoot($user_id);
        return $this->navigationDeep($navs , $breadcrumb);
    }

    /**
     * breadcrumb
     *
     * @param $lastone_id
     * @param $tree
     *
     * @return array
     */
    private function breadcrumb($lastone_id, $tree = [])
    {
        if ($lastone_id === 0) {
            array_unshift($tree, $lastone_id);
            return $tree;
        }else {
            $navigation = $this->navigation->where([
                "id" => $lastone_id
            ])->first();

            array_unshift($tree, $navigation->id);
            return $this->breadcrumb($navigation->pid, $tree);
        }
    }

    /**
     * breadcrumb
     *
     * @param $lastone_id
     * @param $tree
     *
     * @return array
     */
    public function breadcrumbFull($lastone_id, $tree = [])
    {
        if ($lastone_id == 0) {
            $item = [
                'title' => '首页',
                'path' => url('/')
            ];
            array_unshift($tree, $item);
            return $tree;
        }else {
            $condition = [
                "id" => $lastone_id
            ];
            $navigation = $this->navigation->where($condition)->first();

            $item = [
                'title' => $navigation->title,
                'path' => url('/cat',['unicode'=>$navigation->unicode])
            ];
            array_unshift($tree, $item);
            return $this->breadcrumbFull($navigation->pid, $tree);
        }
    }

    /**
     * renderChild
     *
     * @param $pid
     * @param $user_id
     *
     * @return mixed
     */
    public function renderNavChild($pid = 0, $user_id = 0)
    {
        return $this->renderNavs($pid, false, $user_id);
    }

    /**
     * render
     *
     * @param $pid
     * @param $with_child
     * @param $user_id
     *
     * @return mixed
     */
    public function renderNavs($pid = 0, $with_child = false, $user_id = 0)
    {
        $condition = $this->condition([
            'status' => Consts::NORMAL,
            'pid' => $pid
        ], $user_id);

        if ($with_child) {
            $navigations = $this->navigation->where($condition)->with('children')->get();
        }else{
            $navigations = $this->navigation->where($condition)->get();
        }
        return $navigations;
    }

    /**
     * navigation
     *
     * @param $unicode
     * @param $user_id
     *
     * @return mixed
     */
    public function navigation($unicode, $user_id = 0)
    {
        try{
            $navigation = $this->navigation->where($this->condition([
                "unicode" => $unicode,
                "status" => Consts::NORMAL
            ], $user_id))->firstOrFail();
        }catch (\Exception $e){
            throw new NotFoundHttpException();
        }

        return $navigation;
    }

    public function navArticles($navigation_id, $page = 1, $limit = 0, $user_id = 0)
    {
        $articles = $this->article->where(
            $this->condition([
                "navigation_id" => $navigation_id,
                "status" => Consts::PUBLISHED
            ], $user_id)
        )->orderBy('published_at','desc')->paginate($limit,['*'],'page',$page);

        return $articles;
    }

    public function navigationDeep($navigations , $breadcrumb, $deep = 0)
    {
        $htmls = '';
        if (count($navigations) > 0) {
            foreach ($navigations as $navigation) {
                if (count($navigation->children) > 0) {
                    if ($deep == 0) {
                        $html = '<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle ' . active_class(in_array($navigation->id, $breadcrumb)) . '" href="#" id="'. $navigation->unicode .'" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            '. $navigation->title .'
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="'. $navigation->unicode .'">';

                    } else {
                        $html = '<li class="dropdown-submenu dropend">
                                <a class="dropdown-item dropdown-toggle ' . active_class(in_array($navigation->id, $breadcrumb)) . '" href="#">'. $navigation->title .'</a>
                                <ul class="dropdown-menu dropdown-menu-start" data-bs-popper="none">';
                    }

                    $new_deep = $deep + 1;
                    $subHtml = $this->navigationDeep($navigation->children, $breadcrumb, $new_deep );
                    $end_html = '</ul></li>';
                    $htmls .=  ($html . $subHtml . $end_html);
                } else {
                    if ($deep == 0) {
                        $htmls .= '<li class="nav-item">
                                    <a class="nav-link" href="" id="'. $navigation->unicode .'">'. $navigation->title .'</a>
                                 </li>';
                    } else {
                        if ($navigation->type == 'link'){
                            $htmls .= '<li> <a class="dropdown-item" target="_blank" href="'. $navigation->link .'">'. $navigation->title .'</a></li>';
                        } else{
                            $htmls .= '<li> <a class="dropdown-item '. active_class(if_route_param('unicode' , $navigation->unicode)) .'" href="' . route('lavamake.category',['unicode' => $navigation->unicode]) . '">'. $navigation->title .'</a></li>';
                        }
                    }
                }
            }
        }
        return $htmls;
    }

    public function brotherNavs($navigation_pid = 0, $user_id = 0)
    {
        $brothers = $this->navigation->where($this->condition([
            "pid" => $navigation_pid,
            "status" => Consts::NORMAL
        ], $user_id))->get();

        return $this->brotherNavsHtml($brothers);
    }

    protected function brotherNavsHtml($brothers)
    {
        $html = '';
        if(count($brothers) > 0) {
            $html .= '<ul class="nav flex-column">';
            foreach($brothers as $brother){
                $html .= '<li class="nav-item"><a class="nav-link '. active_class(if_route_param('unicode', $brother->unicode)) .'" href="'. route('lavamake.category',['unicode' => $brother->unicode]) .'">'.$brother->title.'</a></li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * parentNav
     *
     * @param $navigation_pid
     * @param $user_id
     *
     * @return null
     */
    public function parentNav($navigation_pid = 0, $user_id = 0)
    {
        if ($navigation_pid == 0) {
            return null;
        }
        $condition = $this->condition([
            "id" => $navigation_pid,
            "status" => Consts::NORMAL
        ], $user_id);

        $parent = $this->navigation->where($condition)->first();
        return $parent;
    }
}
