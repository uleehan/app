<?php
/**
 * Class definition for SolrDocumentService class
 * This class provides an API for accessing a specific Solr document.
 * This can be configured to alter by page ID or wiki ID, but the default behavior is
 * to use the current article ID and wiki ID.
 */
class SolrDocumentService
{
	/**
	 * @var Wikia\Search\Config
	 */
	protected $config;
	
	/**
	 * @var Wikia\Search\QueryService\Factory
	 */
	protected $factory;
	
	/**
	 * Defaults to $wgCityId
	 * @var int
	 */
	protected $wikiId;
	
	/**
	 * Defaults to $wgArticleId
	 * @var int
	 */
	protected $articleId;
	
	/**
	 * Whether we're doing a cross-wiki search on a different core.
	 * @var bool
	 */
	protected $crossWiki = false;
	
	/**
	 * Returns an array version of our search result object
	 * @return array
	 */
	public function getDocument() {
		return (array) $this->getResult();
	}
	
	/**
	 * Returns the result object
	 * @return Wikia\Search\Result;
	 */
	public function getResult() {
		$config = $this->getConfig();
		$config->setQuery( Wikia\Search\Utilities::valueForField( 'id', $this->getDocumentId() ) );
		$queryService = $this->getFactory()->getFromConfig( $config );
		$resultSet = $queryService->search();
		return $resultSet[$this->getDocumentId()];
	}
	
	/**
	 * Allows us to set the current article ID for the document we want.
	 * @param int $id
	 * @return SolrDocumentService
	 */
	public function setArticleId( $id ) {
		$this->articleId = $id;
		return $this;
	}
	
	/**
	 * Lazy-loads wgArticleId in cases where client code has not configured the desired article ID.
	 * @return int
	 */
	public function getArticleId() {
		if ( $this->articleId === null ) {
			global $wgArticleId;
			$this->articleId = $wgArticleId;
		}
		return $this->articleId;
	}
	
	/**
	 * Sets the desired wiki ID for the document we're requesting from Solr.
	 * @param int $id
	 * @return SolrDocumentService
	 */
	public function setWikiId( $id ) {
		$this->wikiId = $id;
		return $this;
	}
	
	/**
	 * Lazy-loads wgCityId into wikiId if it has not already been configured.
	 * @return int
	 */
	public function getWikiId() {
		if ( $this->wikiId === null ) {
			global $wgCityId;
			$this->wikiId = $wgCityId;
		}
		return $this->wikiId;
	}
	
	/**
	 * Returns the document ID for querying as expected by Solr.
	 * @return string
	 */
	protected function getDocumentId() {
		if ( $this->getCrossWiki() ) {
			$id = $this->getWikiId();
		} else {
			$id = sprintf( '%s_%s', $this->getWikiId(), $this->getArticleId() );
		}
		return $id;
	}
	
	/**
	 * Lazy-loads Wikia\Search\Config
	 * @return Wikia\Search\Config
	 */
	protected function getConfig() {
		if ( $this->config === null ) {
			$config = new Wikia\Search\Config;
			$config->setLimit( 1 );
			if ( $this->getCrossWiki() ) {
				$config->setCrossWikiLuceneQuery( true );
			} else {
				$config->setDirectLuceneQuery( true );
			}
			$this->config = $config;
		}
		return $this->config;
	}
	
	/**
	 * Lazy loads Wikia\Search\QueryService\Factory
	 * @return Wikia\Search\QueryService\Factory
	 */
	protected function getFactory() {
		if ( $this->factory === null ) {
			$this->factory = new Wikia\Search\QueryService\Factory();
		}
		return $this->factory;
	}
	
	/**
	 * This refers to cross-wiki or on-wiki.
	 * @param bool $crossWiki
	 * @return SolrDocumentService
	 */
	public function setCrossWiki( $crossWiki ) {
		$this->crossWiki = $crossWiki;
		return $this;
	}
	
	/**
	 * Whether this is a cross-wiki search
	 * @return string
	 */
	public function getCrossWiki() {
		return $this->crossWiki;
	}
	
}