<?php

namespace App\Presenters;

use Nette;
use App\Models\PostsRepository;
use Tracy\Debugger;


final class PostsPresenter extends Nette\Application\UI\Presenter
{
    /** @var PostsRepository @inject */
    public PostsRepository $postsRepository;

    public function renderListAndLastThreeComments()
    {
        // LATERAL METHOD
        Debugger::timer('postsByLateralMethod');
        $this->template->postsByLateralMethod = $this->postsRepository->findPostsAndLastComments(3, PostsRepository::LATERAL_METHOD);
        $this->template->postsByLateralMethodTime = Debugger::timer('postsByLateralMethod');

        // JSON GROUP METHOD
        Debugger::timer('postsByJsonGroupMethod');
        $this->template->postsByJsonGroupMethod = $this->postsRepository->findPostsAndLastComments(3, PostsRepository::JSON_GROUP_METHOD);
        $this->template->postsByJsonGroupMethodTime = Debugger::timer('postsByJsonGroupMethod');

        // TWO QUERIES METHOD
        Debugger::timer('postsByTwoQueriesMethod');
        $this->template->postsByTwoQueriesMethod = $this->postsRepository->findPostsAndLastComments(3, PostsRepository::TWO_QUERIES_METHOD);
        $this->template->postsByTwoQueriesMethodTime = Debugger::timer('postsByTwoQueriesMethod');

    }

    public function renderListAndLastComment()
    {
        $this->template->topics = $this->postsRepository->findPostAndLastComment();
    }
}