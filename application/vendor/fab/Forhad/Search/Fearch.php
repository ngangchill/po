<?php

namespace Forhad\Search;

/**
 * Description of Fearch
 *
 * @author Forhad
 */
class Fearch {

    /**
     * @var Connection
     */
    private $connection;

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
     * Get descriptor for open index.
     *
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    public function index() {
        return $this->connection->getIndex();
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
     * forward every request to ZendSearch\Lucene\Lucene\Index class.
     * call parent function from child class
     * function list:
     * count(), maxDoc(), numDocs(), isdeleted($id), getFieldNames(), getDocument($id),terms(),getDirectory(),delete($id)
     * 
     */

    public function __call($name, $arguments) {
        return call_user_func_array([$this->index(), $name], $arguments);
    }

}
