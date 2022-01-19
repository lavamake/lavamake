<?php

namespace Lavamake\Lavamake\Support\Web\Navigation;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use App\Models\Navigation;
use App\Models\Article;
use Lavamake\Lavamake\Support\Consts;
use Lavamake\Lavamake\Support\Web\AbstractBaseService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NavService extends AbstractBaseService
{
    public function __construct(Repository $config, Request $request)
    {
        parent::__construct($config, $request);
    }

    /**
     * renderRoot
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function renderRoot($user_id = 0)
    {
        return $this->render(0,true,$user_id);
    }

    /**
     * renderRootWithHtml
     *
     * @param $user_id
     *
     * @return string
     */
    public function renderRootWithHtml($user_id = 0)
    {
        $unicode = $this->request->route('unicode');
        $navigation_id = Navigation::where([
            "unicode" => $unicode,
            $this->foreignKey() => $user_id
        ])->value('id');

        $breadcrumb = [];
        if ($navigation_id) {
            $breadcrumb = $this->breadcrumb($navigation_id, []);
        }

        $navs = $this->renderRoot($user_id);
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
    public function breadcrumb($lastone_id, $tree = [])
    {
        if ($lastone_id === 0) {
            array_unshift($tree, $lastone_id);
            return $tree;
        }else {
            $navigation = Navigation::where([
                "id" => $lastone_id
            ])->first();

            array_unshift($tree, $navigation->id);
            return $this->breadcrumb($navigation->pid, $tree);
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
    public function renderChild($pid = 0, $user_id = 0)
    {
        return $this->render($pid, false, $user_id);
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
    public function render($pid = 0, $with_child = false, $user_id = 0)
    {
        if ($with_child) {
            $navigations = Navigation::where([
                $this->foreignKey() => $user_id,
                'status' => 'normal',
                'pid' => $pid
            ])->with('children')->get();
        }else{
            $navigations = Navigation::where([
                $this->foreignKey() => $user_id,
                'status' => 'normal',
                'pid' => $pid
            ])->get();
        }
        return $navigations;
    }

    /**
     * navigation
     *
     * @param $unicode
     * @param $user_id
     *
     * @return $this
     *
     * by Menlain
     * 2022/1/18 - 6:42 PM
     */
    public function navigation($unicode, $user_id = 0)
    {
        try{
            $navigation = Navigation::where([
                "unicode" => $unicode,
                $this->foreignKey() => $user_id,
                "status" => 'normal'
            ])->firstOrFail();
        }catch (\Exception $e){
            throw new NotFoundHttpException();
        }

        return $navigation;
    }

    public function articles($navigation_id, $page = 1, $limit = 0, $user_id = 0)
    {
        $articles = Article::where([
            "navigation_id" => $navigation_id,
            "status" => Consts::PUBLISHED,
            $this->foreignKey() => $user_id
        ])->orderBy('published_at','desc')->paginate($limit,['*'],'page',$page);

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

    public function brothers($navigation_pid = 0, $user_id = 0)
    {
        $brothers = Navigation::where([
            "pid" => $navigation_pid,
            $this->foreignKey() => $user_id,
            "status" => Consts::NORMAL
        ])->get();

        return $this->brotherNavs($brothers);
    }

    protected function brotherNavs($brothers)
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

    public function parent($navigation_pid = 0, $user_id = 0)
    {
        if ($navigation_pid == 0) {
            return null;
        }

        $parent = Navigation::where([
            "id" => $navigation_pid,
            $this->foreignKey() => $user_id,
            "status" => Consts::NORMAL
        ])->first();

        return $parent;
    }
}
