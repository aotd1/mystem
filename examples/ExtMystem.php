<?php
Yii::setPathOfAlias('Mystem', Yii::getPathOfAlias('application.vendor.aotd.mystem.src.Mystem'));

/**
 * @class ExtMystem
 */
class ExtMystem extends CApplicationComponent
{
    /**
     * @var string|array list of words or filePath
     */
    public $falsePositive;
    public $falsePositiveNormalized;
    public $falseNegative;
    public $falseNegativeNormalized;

    public function init()
    {
        $lists = array('falsePositive', 'falsePositiveNormalized', 'falseNegative', 'falseNegativeNormalized');
        foreach ($lists as $listName) {
            if (is_string($this->$listName)) {
                if (!file_exists($this->$listName))
                    throw new CException("List file $listName '{$this->$listName}' not found");
                \Mystem\Article::${$listName.'List'} = array_filter(explode("\n", file_get_contents($this->$listName)), 'trim');
            } elseif (is_array($this->$listName)) {
                \Mystem\Article::${$listName.'List'} = $this->$listName;
            }
        }
        parent::init();
    }

    public function checkArticle($article){
        $article = new \Mystem\Article($article);
        return $article->checkBadWords(false);
    }

}