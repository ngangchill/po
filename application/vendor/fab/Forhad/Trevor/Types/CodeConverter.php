<?php namespace Forhad\Trevor\Types;

class CodeConverter implements ConverterInterface
{
    /**
     * The options we use for html to markdown
     *
     * @var array
     */

    public function toJson(\DOMElement $node)
    {
        $html = $node->ownerDocument->saveXML($node);

        return array(
            'type' => 'text',
            'data' => array( 
                'text' => ' ' . $this->htmlToMarkdown($html)
            )
        );
    }

    public function toHtml(array $data)
    {
		       print_r($data);
      die();
        return \Michelf\MarkdownExtra::defaultTransform($data['text']);
    }

    protected function htmlToMarkdown($html)
    {
        $markdown = new \HTML_To_Markdown($html, $this->options);
        return $markdown->output();
    }
}
