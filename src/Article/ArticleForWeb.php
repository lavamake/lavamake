<?php

namespace Lavamake\Lavamake\Article;

use App\Models\Article;
use Lavamake\Lavamake\Exceptions\ResourceNotFoundException;
use Lavamake\Lavamake\Utils\Consts;

class ArticleForWeb extends ArticleAbstract
{
    /**
     * getFromDB
     *
     * @param $unicode
     * @param $user_id
     *
     * @return mixed
     */
    protected function getFromDB($unicode, $user_id = 0)
    {
        try{
            $condition = $this->condition([
                'unicode' => $unicode,
                'status' => Consts::PUBLISHED
            ], $user_id);
            $article = $this->article->where($condition)->firstOrFail();
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
    public function showArticle($unicode, $user_id = 0)
    {
        $article = $this->getFromDB($unicode, $user_id);
        return $article;
    }
}
