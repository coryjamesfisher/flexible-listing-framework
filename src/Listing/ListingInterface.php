<?php namespace Falafel\Listing;

use Falafel\DataSource\DataSourceInterface;
use Falafel\http\RequestInterface;
use Falafel\http\ResponseInterface;
use Falafel\Renderer\RendererInterface;
use Falafel\Column\ColumnInterface;
use Falafel\Criteria\CriteriaInterface;

/* @note All setters should return the Listing object for chaining */
interface ListingInterface {

	/** @return Listing */
	public function setDataSource(DataSourceInterface $dataSource);

	/** @return DataSourceInterface */
	public function getDataSource();

	/** @return Listing */
	public function setRenderer(RendererInterface $renderer);

	/** @return RendererInterface */
	public function getRenderer();

	/** @return Listing */
	public function addColumnDef($column_id, ColumnInterface $column);

	/** @return Listing */
	public function setColumnDefs($columns);

	/** @return ColumnInterface[] */
	public function getColumnDefs();

	/** @return ColumnInterface */
	public function getColumnDef($column_id);

	/** @return void */
	public function exec(RequestInterface $request, ResponseInterface $response);

	/** @note Utility Functions for Retrieving Data */
	public function getColumnValues(CriteriaInterface $criteria, $column_id);
	public function getRowValues(CriteriaInterface $criteria, $row_number);
}
