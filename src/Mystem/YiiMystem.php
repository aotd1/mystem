<?php
namespace Mystem;

/**
 * @class YiiMystem
 */
class YiiMystem extends \CApplicationComponent
{
    /**
     * @var string|array list of words or filePath
     */
    public $falsePositive;
    public $falsePositiveNormalized;
    public $falseNegative;
    public $falseNegativeNormalized;

    public $heuristicsCheck = true;

    public function init()
    {
        $lists = array('falsePositive', 'falsePositiveNormalized', 'falseNegative', 'falseNegativeNormalized');
        foreach ($lists as $listName) {
            if (is_string($this->$listName)) {
                if (!file_exists($this->$listName)) {
                    throw new \CException("List file $listName '{$this->$listName}' not found");
                }
                Word::${$listName . 'List'} = array_filter(explode("\n", file_get_contents($this->$listName)), 'trim');
            } elseif (is_array($this->$listName)) {
                Word::${$listName . 'List'} = $this->$listName;
            }
        }
        parent::init();
    }

    /**
     * @param string $article
     * @return string[]
     */
    public function checkArticle($article)
    {
        $article = new Article($article);
        $result = $article->checkBadWords(false);
        if (!empty($result) && $this->heuristicsCheck) {
            $result = $this->heuristicsCheck($article);
        }
        return $result;
    }

    /**
     * Make article from nominative not strict words and runs check again
     * @param Article $article
     * @return string[]
     */
    protected function heuristicsCheck(Article $article)
    {
        $nominativeArticle = '';
        foreach ($article->words as $word) {
            if (!$word->variants[0]['strict'] && !$word->checkGrammeme(MystemConst::OTHER_VULGARISM, 0)) {
                $nominativeArticle .= ' ' . $word;
            }
        }
        if ($nominativeArticle === '') {
            return array();
        }

        $newArticle = new Article($nominativeArticle);
        $words = $newArticle->checkBadWords(false);

        $result = array();
        foreach ($words as $original => $word) {
            foreach ($article->words as $originalWord) {
                if ($original === $originalWord->normalized()) {
                    $result[$originalWord->original] = $word;
                    break;
                }
            }
        }
        return $result;
    }
}
