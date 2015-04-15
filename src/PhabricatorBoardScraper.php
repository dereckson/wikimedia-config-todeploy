<?php

require 'TextUtils.php';

class PhabricatorBoardScraper {
    public $url;
    public $boardId;
    public $columnName = 'To deploy';
    public $query = 'open';

    public function __construct ($url, $boardId) {
        $this->url = $url;
        $this->boardId = $boardId;
    }

    public function getURL () {
        return $this->url . "/project/board/$this->boardId/query/$this->query/";
    }

    public function getTasksId () {
        //Gets the HTML content of the specified column
        $url = $this->getURL();
        $data = file_get_contents($url);
        $startString = '<h3 class="phui-action-header-title">' . $this->columnName . '</h3>';
        $stopString = '</div></div></div></div></div></div></li></ul>';
        $content = TextUtils::grab($data, $startString, $stopString);

        //Gets expressions like <a href="/T94142" class="phui-object-item-link" title="Wiktionary namespace translation for Odia / or.wiktionary">
        //If only the numbers are useful, @/T([0-9]*)@ is enough
        $pattern = '@/T([0-9]*)@';
        $matchesCount = preg_match_all($pattern, $content, $matches);
        return $matches[1];
    }
}
