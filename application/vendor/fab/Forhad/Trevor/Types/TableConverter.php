<?php namespace Forhad\Trevor\Types;

/**
 * Description of TableConverter
 *
 * @author Forhad
 */
class TableConverter extends BaseConverter implements ConverterInterface {
    public function toJson(\DOMElement $node) {
        return array(
            'type' => 'image',
            'data' => array(
                'file' => array(
                    'url' => $node->getAttribute('src')
                )
            )
        );
    }
    
    public function toHtml(array $data) {
        $html = '<table class="table table-hover">';
//        echo "<pre>";
//        print_r($data);
//        die();

        foreach ($data['columns'] as $column) {
            $html .= "<div class='col-md-".$column['width']."'>";
            $converter = new Converter();
            $HTMLContent = $converter->toHtml(json_encode(array("data"=>$column["blocks"])));
            $html .= $HTMLContent;
            $html .= "</div>";
        }
        $html .= "</table>";
        return $html;
    }
}
