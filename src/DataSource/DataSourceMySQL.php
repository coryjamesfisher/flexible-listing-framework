<?php namespace Falafel\DataSource;

use Falafel\Criteria\CriteriaInterface;

class DataSourceMySQL implements DataSourceInterface
{

	protected $connection;
	protected $table;

	public function __construct(\PDO $connection, $table)
	{
		$this->connection = $connection;
		$this->table = $table;
	}

	/**
	 * @param CriteriaInterface $criteria
	 * @return \Iterator
	 */
	public function fetchRows(CriteriaInterface $criteria)
	{
		$statement = $this->criteriaToPDOStatement($criteria);

		return new PDOIterator($statement);
	}

	public function fetchColumn(CriteriaInterface $criteria)
	{
		// TODO: Implement fetchColumn() method.
	}

	protected function criteriaToPDOStatement(CriteriaInterface $criteria)
	{
		$queryParams = array();
		$query = $this->criteriaToQuery($criteria, $queryParams);
		return $this->query($query, $queryParams);
	}

	protected function query($query, $queryParams)
	{

		$statement = $this->connection->prepare($query);
		$statement->execute($queryParams);

		return $statement;
	}

	protected function criteriaToQuery(CriteriaInterface $criteria, &$queryParams)
	{
		$where = '';

		$query = 'SELECT ' . implode(',', $criteria->getFields()) . ' FROM ' . $this->table;

		$filters = $criteria->getFilters();

		foreach ($filters as $type => $filter) {
			foreach ($filter as $field => $values) {
				$where .= $this->conditionToWhere($field, $type, $values, $queryParams) . ' AND ';
			}
		}

		if (!empty($where)) {
			$where = substr($where, 0, -5);
			$query .= (strlen($where) ? ' WHERE ' . $where : '');
		}

		return $query;
	}

		

	protected function conditionToWhere($field, $comparator, $values, &$queryParams)
	{

		$where = '';

		if (is_array($values) && count($values) > 1) {
			if ($comparator != 'eq') {

				$where .= '(';
				foreach ($values as $value) {
					$where .= $this->conditionToWhere($field, $comparator, array($value), $queryParams) . ' OR ';
				}
				
				// Trim trailing OR statement
				$where = substr($where, 0, -4);
				$where .= ') ';
				return $where;
			}

			$where .= $field . ' IN(';
			foreach ($values as $value) {
				$param_key = ':' . $field . '_' . count($queryParams);
				$where .= $param_key . ',';
				$queryParams[$param_key] = $value;
			}
			$where = substr($where, 0, -1);
			$where .= ') ';
			return $where;
		}

		$param_key = ':' . $field . '_' . count($queryParams);
		switch ($comparator) {
			case 'eq':
				$where .= $field . ' = ' . $param_key;
				break;

			case 'lt':
				$where .= $field . ' < ' . $param_key;
				break;

			case 'gt':
				$where .= $field . ' > ' . $param_key;
				break;

			case 'startsWith':
				$where .= $field . ' like ' . $param_key;
				$values[0] .= '%';
				break;

			case 'endsWith':
				$where .= $field . ' like ' . $param_key;
				$values[0] = '%' . $values[0];
				break;

			case 'search':
				$where .= $field . ' like ' . $param_key;
				$values[0] = '%' . $values[0] . '%';
				break;

			default:
				throw new \InvalidArgumentException('Invalid comparator used: ' . $comparator);
		}

		$queryParams[$param_key] = array_pop($values);

		return $where;
	}
}
