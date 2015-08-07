<?php namespace Forhad\Trevor\Types;



interface ConverterInterface
{
    public function toJson(\DOMElement $node);
    public function toHtml(array $data);
}
