<?php namespace Forhad\Trevor;

use \Forhad\Trevor\Types\HeadingConverter,
    \Forhad\Trevor\Types\ColumnsConverter,
    \Forhad\Trevor\Types\ButtonConverter,
    \Forhad\Trevor\Types\AccordionConverter,
    \Forhad\Trevor\Types\ListConverter,
    \Forhad\Trevor\Types\BlockquoteConverter,
    \Forhad\Trevor\Types\IframeConverter,
    \Forhad\Trevor\Types\IframeExtendedConverter,
    \Forhad\Trevor\Types\ImageConverter,
    \Forhad\Trevor\Types\ImageExtendedConverter,
    \Forhad\Trevor\Types\BaseConverter;

class ToJsonContext {

    protected $converter = null;

    public function __construct($nodeName) {
        switch ($nodeName) {
            case 'p':
                $this->converter = new ParagraphConverter();
                break;
            case 'h2':
                $this->converter = new HeadingConverter();
                break;
            case 'ul':
                $this->converter = new ListConverter();
                break;
            case 'blockquote':
                $this->converter = new BlockquoteConverter();
                break;
            case 'iframe':
                $this->converter = new IframeConverter();
                break;
            case 'img':
                $this->converter = new ImageConverter();
                break;
            default:
                $this->converter = new BaseConverter();
                break;
        }
    }

    public function getData(\DOMElement $node) {
        return $this->converter->toJson($node);
    }

}
