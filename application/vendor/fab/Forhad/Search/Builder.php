<?php

namespace Forhad\Search;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\Query;

/**
 * Description of Index Builder
 *
 * @author Forhad
 */
class Builder {

    //Zend Document instance
    protected $doc = NULL;
    protected $lucene_normalize_content = TRUE;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Get descriptor for open index.
     *
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    public function index() {
        return $this->connection->getIndex();
    }

    /**
     * Create index instance.
     *
     * @param Connection $connection
     * @param Config $config
     */
    public function __construct($path) {
        $this->connection = new Connection($path);
    }

    /**
     * Destroy the entire index.
     *
     * @return bool
     */
    public function destroy() {
        $this->connection->destroy();
    }

    /*
     * Alias for field()
     *
     * @param   string  $type
     * @param   string  $name
     * @param   string  $value
     * @param   bool    $normalize
     * @return  Lucene
     */

    public function add_field($type = '', $name = '', $value = '', $normalize = NULL) {
        return $this->field($type, $name, $value, $normalize);
    }

    /**
     * Add a field to the document
     *
     * @param   string  $type
     * @param   string  $name
     * @param   string  $value
     * @param   bool    $normalize
     * @return  Lucene
     */
    public function field($type = '', $name = '', $value = '', $normalize = NULL) {
        // Has the document been created?
        if (!$this->doc) {
            $this->doc = new Document();
        }

        // Field type
        $type = $this->_field_type($type);

        // Continue only if it is a value for field type and it has a key
        if ($type AND $name) {
            // Normalize content
            if ($normalize === TRUE OR ( $normalize === NULL AND $this->lucene_normalize_content === TRUE)) {
                $value = $this->_normalize($value);
            }
            $this->doc->addField(Document\Field::$type($name, $value, 'utf-8'));
        }

        return $this;
    }

    /**
     * Finalize the document
     *
     * @return  Lucene
     */
    public function set_document() {
        // Only set the document if there is something in it
        if ($this->doc) {
            // Set document
            $this->index()->addDocument($this->doc);

            // Reset the document so more can be added
            $this->doc = NULL;
        }

        return $this;
    }

    /**
     * Commit the document to the index
     *
     * @param   bool    $optimize
     * @return  Lucene
     */
    public function commit($optimize = TRUE) {
        $this->index()->commit();

        if ($optimize) {
            return $this->optimize();
        }

        return $this;
    }

    /**
     * Optimize the index
     *
     * @return  Lucene
     */
    public function optimize() {
        $this->index()->optimize();
        return $this;
    }

    /**
     * Document field type
     *
     * @param   string  $type
     * @return  string
     */
    private function _field_type($type = '') {
        /**
         * UnStored
         *      fields are tokenized and indexed, but not stored in the index. Large amounts of text are best indexed
         *      using this type of field. Storing data creates a larger index on disk, so if you need to search but not
         *      redisplay the data, use an UnStored field. UnStored fields are practical when using a Lucene index in
         *      combination with a relational database. You can index large data fields with UnStored fields for
         *      searching, and retrieve them from your relational database by using a separate field as an identifier.
         *      The content in the node body is a good candidate for UnStored fields.
         *
         * Keyword
         *      fields are stored and indexed, meaning that they can be searched as well as displayed in search
         *      results. They are not split up into separate words by tokenization. Enumerated database fields usually
         *      translate well to Keyword fields in Search Lucene API. Items like node IDs are best stored in keyword
         *      fields.
         *
         * UnIndexed
         *      fields are not searchable, but they are returned with search hits. Database timestamps, primary keys,
         *      file system paths, and other external identifiers are good candidates for UnIndexed fields.
         *
         * Text
         *      fields are stored, indexed, and tokenized. Text fields are appropriate for storing information like
         *      subjects and titles that need to be searchable as well as returned with search results.
         *
         * Binary
         *      fields are not tokenized or indexed, but are stored for retrieval with search hits. They can be used
         *      to store any data encoded as a binary string, such as an image icon.
         *
         *
         * Field Type   Stored  Indexed     Tokenized   Binary
         * ----------   ------  -------     ---------   ------
         * Keyword      Yes     Yes         No          No
         * UnIndexed    Yes     No          No          No
         * Binary       Yes     No          No          Yes
         * Text         Yes     Yes         Yes         No
         * UnStored     No      Yes         Yes         No
         */
        switch (strtolower($type)) {
            case 'keyword':
                return 'Keyword';
                break;
            case 'unindexed':
                return 'UnIndexed';
                break;
            case 'binary':
                return 'Binary';
                break;
            case 'text':
                return 'Text';
                break;
            case 'unstored':
                return 'UnStored';
                break;
            default:
                return FALSE;
        }
    }

    /**
     * Normalize data by replacing foreign characters
     *
     * @param   string  $input
     * @return  string
     */
    private function _normalize($input = '') {
        return strtr($input, array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r'
        ));
    }

}
