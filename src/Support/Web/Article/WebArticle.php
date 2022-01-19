<?php

namespace Lavamake\Lavamake\Support\Web\Article;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Lavamake\Lavamake\Events\ArticleViewed;
use Lavamake\Lavamake\Exceptions\ResourceNotFoundException;
use Lavamake\Lavamake\Listeners\ArticleViewedListener;
use App\Models\Article;
use Lavamake\Lavamake\Support\Consts;
use Lavamake\Lavamake\Support\Web\AbstractBaseService;

class WebArticle extends AbstractBaseService
{
    /**
     * app events
     *
     * @var Dispatcher
     */
    protected $events;

    public function __construct(Repository $config,Request $request , Dispatcher $events)
    {
        parent::__construct($config, $request);
        $this->events = $events;

        $this->events->listen(ArticleViewed::class,ArticleViewedListener::class);
    }

    /**
     * getFromDB
     *
     * @param $unicode
     * @param $user_id
     *
     * @return mixed
     */
    public function getFromDB($unicode, $user_id = 0)
    {
        try{
            $article = Article::where([
                $this->foreignKey() => $user_id,
                'unicode' => $unicode,
                'status' => Consts::PUBLISHED
            ])->firstOrFail();
        }catch (\Exception $e){
            throw new ResourceNotFoundException();
        }
        return $article;
    }

    /**
     * show
     *
     * @param $unicode
     * @param $user_id
     *
     * @return mixed|void|null
     */
    public function show($unicode, $user_id = 0)
    {
        $article = $this->getFromDB($unicode, $user_id);
        $this->events->dispatch(new ArticleViewed($unicode));
        return $article;
    }
}
