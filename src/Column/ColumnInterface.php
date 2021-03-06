<?php namespace Falafel\Column;

use Falafel\Renderer\ColumnRendererInterface;

interface ColumnInterface
{
	public function getKey();
	public function setKey($key);
	public function getRenderer();
	public function setRenderer(ColumnRendererInterface $renderer);
}
