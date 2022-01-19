<?php

namespace Lavamake\Lavamake\Policies;

use Lavamake\Lavamake\Contracts\LavaMakeAuth;
use App\Models\Article;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(LavaMakeAuth $lavaMakerAuth)
    {
        return $lavaMakerAuth->isLMAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Lavamake\Lavamake\Contracts\LavaMakeAuth $lavaMakeAuth
     * @param  \App\Models\Article            $article
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(LavaMakeAuth $lavaMakeAuth, Article $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->app_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Lavamake\Lavamake\Contracts\LavaMakeAuth $lavaMakeAuth
     * @param  \App\Models\Article            $article
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(LavaMakeAuth $lavaMakeAuth, Article $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->app_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Lavamake\Lavamake\Contracts\LavaMakeAuth $lavaMakeAuth
     * @param  \App\Models\Article            $article
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(LavaMakeAuth $lavaMakeAuth, Article $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->app_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Lavamake\Lavamake\Contracts\LavaMakeAuth $lavaMakeAuth
     * @param  \App\Models\Article            $article
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(LavaMakeAuth $lavaMakeAuth, Article $article)
    {
        return $lavaMakeAuth->getLMIdentifier() === $article->app_id;
    }
}
