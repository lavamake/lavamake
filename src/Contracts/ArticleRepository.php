<?php

namespace Lavamake\Lavamake\Contracts;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Lavamake\Lavamake\Exceptions\UserModelNotBeSupportedException;
use Lavamake\Lavamake\Support\Consts;
use App\Models\Article;

abstract class ArticleRepository
{
    use AuthorizesRequests;

    protected $config;
    protected $request;

    protected $user;

    public function __construct(Repository $config, Request $request)
    {
        if (!$request->user() instanceof LavaMakeAuth) {
            throw new UserModelNotBeSupportedException('user type error');
        }

        $this->config = $config['lavamake'];
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function userIdentifier()
    {
        return $this->user->getLMIdentifier();
    }

    protected function foreignKey()
    {
        return $this->config['foreign_key'];
    }

    public function show($article_id)
    {
        return Article::findOrFail($article_id);
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
        $data[$this->foreignKey()] = $this->userIdentifier();
        return Article::create($data);
    }

    /**
     * publish article
     *
     * @param Article $article
     *
     * @return Article
     */
    public function publish(Article $article)
    {
        $article->published_at = Carbon::now()->toDateTimeString();
        $article->status = Consts::PUBLISHED;
        $article->save();
        return $article;
    }

    /**
     * put article to draft
     *
     * @param Article $article
     *
     * @return Article
     */
    public function draft(Article $article)
    {
        $article->status = Consts::DRAFT;
        $article->save();
        return $article;
    }

    protected function fillable()
    {
        return (new Article())->getFillable();
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
        $data[$this->foreignKey()] = $this->userIdentifier();
        return Article::find($$article_id)->update($data);
    }
}
