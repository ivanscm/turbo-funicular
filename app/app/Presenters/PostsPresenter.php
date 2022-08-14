<?php

namespace App\Presenters;

use App\Services\Moderation;
use Nette;
use App\Models\PostsRepository;
use Tracy\Debugger;


final class PostsPresenter extends Nette\Application\UI\Presenter
{
    /** @var PostsRepository @inject */
    public PostsRepository $postsRepository;

    /** @var Moderation @inject */
    public Moderation $moderationService;

    protected function createComponentRemoveWordsInComment(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();
        $form->addHidden('id');
        $multiplier = $form->addMultiplier('words', function (Nette\Forms\Container $container, Nette\Forms\Form $form) {
            $container->addText('word', 'Слово')
                ->setRequired('Укажите слово для удаления');
        }, 0, 3);

        $multiplier->addCreateButton('Добавить слово для удаления');
        $multiplier->addRemoveButton('Удалить')
            ->addClass('btn btn-danger');
        $form->addTextArea('comment', 'Комментарий')
            ->setRequired('Комментарий не должен быть без текста.');

        $form->addSubmit('removeWords', 'Удалить указанные слова')
            ->onClick[] = [$this, 'removeWordsInComment'];
        $form->addSubmit('save', 'Сохранить')
            ->onClick[] = [$this, 'saveComment'];
        return $form;
    }

    public function removeWordsInComment(Nette\Forms\Controls\Button $button, $data)
    {
        $form = $button->getForm();
        $values = $form->getValues();
        $words = [];
        foreach ($values->words as $word) {
            $words[] = $word->word;
        }
        $result = $this->moderationService->findAndRemove($words, $values->comment);
        $form['comment']->setValue($result);
    }

    public function saveComment(Nette\Forms\Controls\Button $button, $data)
    {
        $form = $button->getForm();
        $values = $form->getValues();
        $this->postsRepository->saveCommentText($values->id, $values->comment);
        $this->flashMessage('Комментарий был успешно изменен.', 'is-success');
    }


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

    public function renderModerateComments()
    {
        $this->template->comments = $this->postsRepository->findAllComments();
    }

    public function renderRemoveWordsInComment($id)
    {
        $this->template->comment = $this->postsRepository->getCommentById($id);
        if (!$this->template->comment) {
            $this->error('Комментарий не найден! Вероятно он был удален или никогда не существовал.');
        }
        $this['removeWordsInComment']->setDefaults($this->template->comment);
    }

    public function renderReplaceWordsInComment($id)
    {

    }
}