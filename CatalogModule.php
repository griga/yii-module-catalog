<?php

class CatalogModule extends CWebModule
{
	public function init()
	{
		$this->setImport(array(
			'catalog.models.*',
			'catalog.components.*',
		));
	}
}
