<?php namespace Forhad\Acme\Highlighting;

use Forhad\Acme\Analyzer\Config as AnalyzerConfig;
use Forhad\Acme\Query\Runner;

/**
 * Class Html
 * @package Forhad\Rfl
 */
class Html
{
    /**
     * @var Runner
     */
    private $queryRunner;

    /**
     * @var Highlighter
     */
    private $highlighter;

    /**
     * @var AnalyzerConfig
     */
    private $analyzerConfig;

    public function __construct(Runner $queryRunner, Highlighter $highlighter, AnalyzerConfig $analyzerConfig)
    {
        $this->queryRunner = $queryRunner;
        $this->highlighter = $highlighter;
        $this->analyzerConfig = $analyzerConfig;
    }

    /**
     * Highlight matches in HTML fragment.
     *
     * @param string $html
     * @return string
     */
    public function highlight($html)
    {
        $highlighted = '';

        $lastQuery = $this->queryRunner->getLastQuery();

        if (!empty($lastQuery)) {

            $this->analyzerConfig->setHighlighterAnalyzer();
            $highlighted = $lastQuery->htmlFragmentHighlightMatches($html, 'utf-8', $this->highlighter);
            $this->analyzerConfig->setDefaultAnalyzer();
        }

        return !empty($highlighted) ? $highlighted : $html;
    }
}
