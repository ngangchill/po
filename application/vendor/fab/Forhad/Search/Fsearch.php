<?php namespace Forhad\Search;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\Query;

/**
 * This is a Zend Search lucene library for my APp;
 *
 * @author Forhad
 */
class Fsearch {

    // default directory where index file live
    protected $index_dir;
    //index instance
    protected $index;
    //Zend Document instance
    protected $doc = NULL;
    protected $lucene_normalize_content = TRUE;
    
    /*
     * constructor 
     */
    function __construct() {
        //set index directory
        $this->index_dir = APPPATH . 'searchIndex/';
    }
    /*
     * create a new index for search
     */
    public function create($dir) {
        $this->index = Lucene::create($this->index_dir . $dir);
        return $this;
    }
    /*
     * open an index
     */
    public function open($dir) {
        $this->index = Lucene::open($this->index_dir . $dir);
        return $this;
    }
    /*
     * delete an entry from index
     */
    public function delete($id) {
        $this->index->delete($id);
        return $this;
    }
    /*
     * delete an index
     */
    public function clearDirectory($dirName) {
        $dir = $this->index_dir . $dirName;
        
        //dd(file_exists($dir));
        if (!file_exists($dir) || !is_dir($dir)) {
            return;
        }
        // remove files from temporary directory
        $di = opendir($dir);
        while (($file = readdir($di)) !== false) {
            if (!is_dir($dir . '/' . $file)) {
                @unlink($dir . '/' . $file);
            }
        }
        closedir($di);
    }
    /*
     * count total entries
     */
    public function count() {        
        return $this->index->count();
        //return $this;
    }
    /*
     * maxDoc
     */
    public function maxdoc() {
         return $this->index->maxDoc();
    }
    /*
     * numdoc
     */
    public function numdocs() {       
        return $this->index->numDocs();
    }
    /*
     * is deleted an entry ? TRUE : FALSE
     */
    public function isdeleted($id) {
        return $this->index->isDeleted($id);
    }
    /*
     * maxbuffereddocs
     */
    public function maxbuffereddocs() {
        return $this->index->getMaxBufferedDocs();
    }
    /*
     * MaxMergeDocs
     */
    public function MaxMergeDocs() {
        return $this->index->getMaxMergeDocs();
    }
    /*
     * default search field
     */
    public function DefaultSearchField() {
        return Lucene::getDefaultSearchField();
    }
    /*
     * Search for entries
     * @param $query  things for search
     * @param $parse bolean
     */
    public function find($query, $parse = FALSE) {
        if($parse){
            $query = \ZendSearch\Lucene\Search\QueryParser::parse('first_name:'.$query);
        }
        
        return $this->index->find($query);
    }
    /*
     * get fields name for an index
     */
    public function getfieldnames() {
        return $this->index->getFieldNames();
    }
    /*
     * get an entry
     * @param $id  integer
     */
    public function getdocument($id) {
        //$this->open($index);
        return $this->index->getDocument($id);
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
            $this->index->addDocument($this->doc);

            // Reset the document so more can be added
            $this->doc = NULL;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Alias for set_document()
     *
     * @return  Lucene
     */
    public function document() {
        return $this->set_document();
    }
    /**
     * Commit the document to the index
     *
     * @param   bool    $optimize
     * @return  Lucene
     */
    public function commit($optimize = TRUE) {
        $this->index->commit();

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
        $this->index->optimize();
        return $this;
    }
    /*
     * Does index has deletions?
     */
    public function hasdeletions($dir = NULL) {
        return $this->index->hasDeletions();
    }
    /*
     * oc freq
     */
    public function docfreq()
    {
        return $this->index->docFreq(new Index\Term('packages', 'contents'));
    }
    /*
     * get similarity
     */
    public function getsimilarity()
    {
        return $this->index->getSimilarity();
    }
    
    public function testNorm()
    {
        $this->index->norm(3, 'contents');
    }
    public function term() {
        return $this->index->terms();
    }
    public function currentterm() {
        return $this->index->terms();
    }
    public function nextterm() {
        return $this->index->terms();
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
