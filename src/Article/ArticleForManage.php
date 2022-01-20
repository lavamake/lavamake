<?php

namespace Lavamake\Lavamake\Article;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Lavamake\Lavamake\Config\ConfigInterface;
use Lavamake\Lavamake\Contracts\LavaMakeAuth;
use Lavamake\Lavamake\Exceptions\HttpServerException;
use Lavamake\Lavamake\Exceptions\UserModelNotBeSupportedException;
use Lavamake\Lavamake\Utils\Consts;

class ArticleForManage extends ArticleAbstract
{
    use AuthorizesRequests;

    protected $user;

    public function __construct(Model $article, ConfigInterface $config, Request $request)
    {
        parent::__construct($article, $config, $request);

        if (! $request->user() instanceof LavaMakeAuth) {
            throw new UserModelNotBeSupportedException('user type error');
        }

        $this->user = $request->user();
    }

    protected function userIdentifier()
    {
        return $this->user->getLMIdentifier();
    }

    public function show($article_id)
    {
        return $this->article->findOrFail($article_id);
    }

    /**
     * Create Article
     *
     * This method of creating articles only operates to save data,
     * please use this method to store articles
     * after processing the data
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $fillable = $this->fillable();
        $data = $request->only($fillable);
        $data[$this->config->foreignKey()] = $this->userIdentifier();
        return $this->article->create($data);
    }

    /**
     * publish
     *
     * @param $article
     *
     * @return mixed
     */
    public function publish($article)
    {
        $this->authorize('publish','article');

        if (! $article instanceof Model || $article->getTable() !== 'articles')
        {
            throw new HttpServerException('data model error');
        }

        $article->published_at = Carbon::now()->toDateTimeString();
        $article->status = Consts::PUBLISHED;
        $article->save();
        return $article;
    }

    /**
     * draft
     *
     * @param $article
     *
     * @return mixed
     */
    public function draft($article)
    {
        $this->authorize('draft','article');

        if (! $article instanceof Model || $article->getTable() !== 'articles')
        {
            throw new HttpServerException('data model error');
        }

        $article->status = Consts::DRAFT;
        $article->save();
        return $article;
    }

    /**
     * delete
     *
     * @param $article
     *
     * @return bool|null
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete($article)
    {
        $this->authorize('delete','article');

        if (! $article instanceof Model || $article->getTable() !== 'articles')
        {
            throw new HttpServerException('data model error');
        }

        return $article->delete();
    }

    /**
     * restore
     *
     * @param $article
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore($article)
    {
        $this->authorize('restore','article');

        if (! $article instanceof Model || $article->getTable() !== 'articles')
        {
            throw new HttpServerException('data model error');
        }

        return $article->restore();
    }

    /**
     * forceDelete
     *
     * @param $article
     *
     * @return bool|null
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function forceDelete($article)
    {
        $this->authorize('forceDelete','article');

        if (! $article instanceof Model || $article->getTable() !== 'articles')
        {
            throw new HttpServerException('data model error');
        }

        return $article->forceDelete();
    }

    protected function fillable()
    {
        return $this->article->getFillable();
    }

    /**
     * update article
     *
     * @param Request $request
     * @param         $article_id
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $article_id)
    {
        $this->authorize('update','article');

        $fillable = $this->fillable();
        $data = $request->only($fillable);
        $data[$this->config->foreignKey()] = $this->userIdentifier();
        return $this->article->find($article_id)->update($data);
    }

    /**
     * Published Article Number
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:48 AM
     */
    public function userPublishedArticleNumber()
    {
        return $this->article->where([
            $this->config->foreignKey() => $this->user->getLMIdentifier(),
            'status' => Consts::PUBLISHED
        ])->count();
    }

    /**
     * Draft Article Number
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:48 AM
     */
    public function userDraftArticleNumber()
    {
        return $this->article->where([
            $this->config->foreignKey() => $this->user->getLMIdentifier(),
            'status' => Consts::DRAFT
        ])->count();
    }

    /**
     * All article Number(published and draft)
     *
     * @return mixed
     *
     * by Menlain
     * 2022/1/18 - 7:49 AM
     */
    public function userArticleNumber()
    {
        return $this->articlewhere([
            $this->config->foreignKey() => $this->user->getLMIdentifier(),
        ])->count();
    }
}
